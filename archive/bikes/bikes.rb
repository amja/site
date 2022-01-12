require 'sinatra'
require 'sinatra/reloader' if development?
require 'xmlsimple'
require 'rubygems'
require 'net/http'
require 'json'
require 'active_support/all'
require 'uri'
require 'digest/md5'

set :port, 80
set :public_folder, 'public'
# set :environment, :production

get '/meta.json' do
  erb :meta
end

get '/edition/' do
  if params[Date.today.strftime("%A").downcase] == "1"
    data = XmlSimple.xml_in(Net::HTTP.get("www.tfl.gov.uk","/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml?app_id=4e60e33c&app_key=84692d37efa544cd0190189fa136dc06"), { 'KeyAttr' => 'name' })
    @chosen = URI.decode(params[:station])
    @station = data["station"].select {|station| station["name"][0] == @chosen}
    etag Digest::MD5.hexdigest(@station[0]["name"][0]+@station[0]["nbBikes"][0]+@station[0]["nbEmptyDocks"][0])
    erb :edition
  end
end

get '/sample/' do
  etag Digest::MD5.hexdigest(Time.now.strftime('%d%m%y'))
  erb :sample
end

post '/validate_config/' do
  response = {}
  response[:errors] = {}
  response[:valid] = true

  if params[:config].nil?
    return 400, "You didn't post a config"
  end

  settings = JSON.parse(params[:config])

  if settings['day'].nil?
    response[:valid] = false
    response[:errors].push("Please select delivery day(s)")
  end

  content_type :json
  response.to_json
end
