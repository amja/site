#config
require 'sinatra'
require 'sinatra/reloader' if development?
require 'rubygems'
require 'json'
require 'encryptor'
require 'yaml'
require 'oauth'
require 'faraday'
require 'faraday_middleware'

#Allow Sinatra to make session cookies
enable :sessions, :logging
set :session_secret, '*&(^B2adf34'
keys = YAML::load_file("../keys/ptt_config.yaml") # Load API and OAuth keys.

# Create new OAuth consumer (using OAuth gem)
consumer = OAuth::Consumer.new( keys['consumer_key'], keys['consumer_secret'], {
        :site               => "https://www.tumblr.com",
        :scheme             => :header,
        :http_method        => :post,
        :request_token_path => "/oauth/request_token",
        :access_token_path  => "/oauth/access_token",
        :authorize_path     => "/oauth/authorize"
})

def requestfilm(domain)
  conn = Faraday.new
  response = conn.get domain
  return response.body
end

def tumblr_request()
  keys = YAML::load_file("config.yaml")

  # Make new consumer (tumblr three legs have different url from api requests)
  consumer = OAuth::Consumer.new( keys['consumer_key'], keys['consumer_secret'], {site: "https://api.tumblr.com"})

  # Populate OAuth::AccessToken ready for request
  access_token = OAuth::AccessToken.new consumer, Encryptor.decrypt(request.cookies['one'], key: keys['secret']), Encryptor.decrypt(request.cookies['two'], key: keys['secret'])
end

get '/' do
  erb :search
end

get '/search' do
  # Get parameter for next page mechanism
  more = params['next'].to_f

  # Escape user entered params
  film = URI.escape(params[:search])

  # Get results from api and parse.
  results = JSON.parse(requestfilm("http://api.themoviedb.org/3/search/movie?query=#{film}&api_key=#{keys['movie_db']}"))["results"].sort_by { |e| [-e['vote_count'], -e['vote_average']] }

  return "<p>No films found</p>" if results[0].nil?

  # Initialise array
  @films = []

  # Set i to more unless more is 0
  more != 0 ? i = more : i = 0

  # Iterate through results
  results.count.times do
    break if i > more+2
    @films << results[i]
    i = i+1
  end
  erb :results
end

# Get posters from theMovieDb using the movie id.
get '/posters' do
  session['name'] = params[:name]
  id = params[:id]
  # @lang = JSON.parse(requestfilm("http://api.themoviedb.org/3/movie/#{id}?api_key=#{keys['movie_db']}"))["spoken_languages"][0]["iso_639_1"]
  @film = JSON.parse(requestfilm("http://api.themoviedb.org/3/movie/#{id}/images?api_key=#{keys['movie_db']}"))["posters"].sort_by { |e| -e['vote_average'] }.select{|poster| poster["iso_639_1"] == "en"}
  erb :choose
end

get '/tags' do
  unless params[:tags].nil? then
    session['tags'] = params[:tags] + "," + session['name']
  else
    session['tags'] = session['name']
  end
  # #Make session variables for poster data
  session['url'] = params[:url]
  "yoyoyo"
end

get '/stars' do
  session['tags'] << "," + params[:stars]
  redirect "/auth"
end

# Begin OAuth authentication
get '/auth' do
    #does the config already contain an oauth_token?
    if(request.cookies['one'].nil?)

      # Leg 1: get the request token
      request_token = consumer.get_request_token(oauth_callback: "http://ptt.amos.im/auth2")
      session['rt'] = request_token.token
      session['rs'] = request_token.secret

      redirect request_token.authorize_url
    else
      redirect '/blog'
    end
end

get '/auth2' do
  # Rebuild Request token object
  request_token = OAuth::RequestToken.new(consumer, session['rt'], session['rs'])
  # Get access token
  access_token = request_token.get_access_token(oauth_verifier: params[:oauth_verifier])
  # Make cookies from access tokens
  response.set_cookie 'one', {value: Encryptor.encrypt(access_token.token, key: keys['secret']), max_age: "#{60*1000*60*24*100}"}
  response.set_cookie 'two', {value: Encryptor.encrypt(access_token.secret, key: keys['secret']), max_age: "#{60*1000*60*24*100}"}
  redirect '/blog'

end

get '/blog' do

  if request.cookies['blog'].nil?

    #Make tumblr request for blogs data
    response = tumblr_request().get "/v2/user/info"

    # Put all of user's blogs in array
    @urls = Hash.new
    JSON.parse(response.body)["response"]["user"]["blogs"].each do |blog|
      @urls["#{blog["name"]}"] = blog["url"]
    end
    erb :blog
  else
    #Otherwise, just go to done
    redirect "/done?blog=#{request.cookies['blog']}"
  end
end

get '/done' do
  #Get blog name from cookie
  blog = params[:blog]
  response.set_cookie 'blog', {value: blog, max_age: "#{60*1000*60*24*100}"}
  blog.slice! "https://"

  #Make request
  json = tumblr_request().post("/v2/blog/#{blog}post", {type: "photo", source: session['url'], tags: session['tags']}, { 'Accept'=>'application/json', 'Content-Type' => 'application/json' })
  #Clear session
  session.clear
  # Check for errors
  response = JSON.parse(json.body)


  if response["meta"]["status"].to_s == "201" then
    "<link rel='stylesheet' type'text/css' href='style.css'><h2>Success!</h2><p><a href='https://#{blog}' class='norm'>Visit your blog</a> or <a href='/' class='norm'>start again</a></p>"
  else
    "Something went wrong: <pre>#{response["response"]["errors"][0]}</pre>"
  end
end
