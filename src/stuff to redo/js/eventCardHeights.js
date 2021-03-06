var events = [
	{
		title: "Check In",
		start: "9:00AM",
		end: "2:00PM"
	},
	{
		title: "Breakfast",
		start: "9:00AM",
		end: "11:00AM"
	},
	{
		title: "Design as a Team Sport",
		start: "11:15AM",
		end: "12:15PM"
	},
	{
		title: "Lunch",
		start: "12:30PM",
		end: "1:30PM"
	},
	{
		title: "Saturday Keynote Opener - Killing the Cat",
		start: "1:45PM",
		end: "2:30PM"
	}
];
events.forEach(function(e){
	var eventLength = parseTime(e.end) - parseTime(e.start);
	console.log(eventLength);
});

function parseTime(time){
	var timeString = time.split(':');
	var hour = parseInt(timeString[0]);
	var minutes = parseInt(timeString[1].substr(0, 2));
	var period = timeString[1].substr(2,3);

	if(period == "PM" && hour < 12)
		hour = (hour + 12);

	var totalMinutes = 60*hour + minutes;
	return totalMinutes;
}