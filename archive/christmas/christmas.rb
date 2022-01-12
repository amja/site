require 'sinatra'
require 'weekdays'
require 'time'
def separate_comma(number)
  number.to_s.chars.to_a.reverse.each_slice(3).map(&:join).join(",").reverse
end

get '/' do
	unless params[:time]  and params[:date]
		erb :form
	else
		if params[:time] == "" or params[:date] == ""
			erb :form
		else
		if Date.parse(params[:date])-Date.today < 0
			erb :form
		else
		@weekdays = Date.today.weekdays_until Date.parse(params[:date])
		@days = (Date.parse(params[:date]) - Date.today).to_i
		@hours = params[:time].to_i		
		@schoolhours = @weekdays*@hours
		@minutes = @weekdays*@hours*60
		@orbits = (@days*24*60/92.89).round
		@travels = separate_comma((@days*24*60*60*30).round)
		@length = (@days*250/100).round
		@deaths = separate_comma(@days*6390*24)
		erb :data
		end
		end
	end
end
