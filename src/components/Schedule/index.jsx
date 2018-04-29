import React from "react";

class Schedule extends React.Component {
  render() {
    return(
      <div className="schedule_main flex flex-column">
        <div className="day_selector_cont flex-justify-center">
          <div id="fri_text" className="day_option schedule-filter-active flex flex-column flex-justify-center">
            <p>Friday</p>
          </div>
          <div id="sat_text" className="day_option flex flex-column flex-justify-center">
            <p>Saturday</p>
          </div>
          <div id="sun_text" className="day_option flex flex-column flex-justify-center">
            <p>Sunday</p>
          </div>
        </div>

        <div id="schedule" className="schedule_cont flex flex-column"></div>
      </div>
    );
  }
}

export default Schedule;
