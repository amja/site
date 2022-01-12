require 'rubygems'
require 'nokogiri'
require 'json'
require 'net/http'

data = Nokogiri::XML(Net::HTTP.get("www.tfl.gov.uk","/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml?app_id=4e60e33c&app_key=84692d37efa544cd0190189fa136dc06"))
@names = data.xpath("//station//name")
hash = {}
@names.each do |name|
  temp = {name.content => name.content}
  hash.merge!(temp)
end
jsonpart = hash.sort_by{|word| word[0].downcase}.map{|k,v| "[\"#{k}\", \"#{v}\"]"}.join(',')

IO.write(File.join(__dir__, 'views/meta.erb'), File.open(File.join(__dir__, 'views/meta.erb')) {|f| f.read.gsub(/\["Abbey(.*)"\]/,jsonpart)})
