#config
require 'sinatra'
require 'sinatra/reloader' if development?
require 'net/http'

get '/:method/:period' do
  Net::HTTP.get(URI("http://ws.audioscrobbler.com/2.0/?method=#{params[:method]}&user=amosjackson&api_key=XXX&period=#{params[:period]}&format=json"))

end

get '/' do
  erb :music
end

get '/tracks' do
  erb :tracks
end