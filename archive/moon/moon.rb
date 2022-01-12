#config
require 'sinatra'
require 'sinatra/reloader' if development?
require 'rubygems'
require 'json'
require 'net/http'
require 'active_support/all'
require 'digest/sha2'

set :port, 80
set :public_folder, 'public'
set :environment, :production




get '/meta.json' do

	erb :meta
end

get '/edition/' do

	return 400 if params['local_delivery_time'].nil?
	times = {2013 =>[[27,05],[25,21],[27,10],[25,21],[25,06],[23,13],[22,10],[21,03],[19,13],[18,01],[17,16],[17,10]],2014 =>[[16,4],[14,23],[16,17],[15,07],[14,19],[13,04],[12,11],[10,18],[9,01],[8,10],[6,22],[6,12]],2015=>[[5,04],[4,23],[5,18],[4,12],[4,03],[2,16],[2,02],[31,10],[29,18],[28,02],[27,12],[25,22],[25,11]]}
	#,2016=>[24,22,23,22,21,20,20,18,16,16,14,14],2017=>[12,11,12,11,10,9,9,7,6,5,4,3],2018=>[2,31,2,31,30,29,28,27,26,25,24,23,22]

	# sets variables (got from local delivery time)
	date = params['local_delivery_time'].to_s
	year = date[0..3].to_i
	month = date[5..6].to_i
	zone = date[19..21].to_i
	day = date[8..9].to_i
	# If the the timezone makes the time of fullmoon before midnight, make before = true
	if zone + times[year][month-1][1] <= 5
		before = true
	else
		before = false
	end

	#Moon defaults to false
	@moon = false
	# If time has not been put back, do as normal. If it has, take 1 away from the day it triggers on.
	if before == false
		@moon = true if day == times[year][month-1][0]
	else
		@moon = true if day == times[year][month-1][0]-1
	end
	#newly required http content type
	content_type "text/html; charset=utf-8"
	#http etag
	etag Digest::SHA2.hexdigest(params['local_delivery_time'])
	return if @moon == false
	erb :moon
end

#security
get '/' do

	no peeking!
end


get '/sample/' do

	erb :sample
end


post '/validate_config/' do

  response = {}
  response[:errors] = []
  response[:valid] = true

  content_type :json
  response.to_json
end
