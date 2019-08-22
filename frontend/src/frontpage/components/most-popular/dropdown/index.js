import React from "react";

import { MostPopularDropdowOption } from "./option";
import { userPreferenceDeltaToString } from "../../../utilities/utils";
import {
  DELTA_MOST_POPULAR_ALL,
  DELTA_MOST_POPULAR_MONTH,
  DELTA_MOST_POPULAR_TODAY,
  DELTA_MOST_POPULAR_WEEK,
  DELTA_MOST_POPULAR_YEAR
} from "../../../consts";

export const MostPopularDropdown = ({ selectedButton, toggleDropdown, changeDelta, open }) => {

  const dropdownOptionDeltas = [
    DELTA_MOST_POPULAR_TODAY,
    DELTA_MOST_POPULAR_WEEK,
    DELTA_MOST_POPULAR_MONTH,
    DELTA_MOST_POPULAR_YEAR,
    DELTA_MOST_POPULAR_ALL,
  ];

  return (
    <div className={`btn-group ${open ? 'open': ''}`} onClick={toggleDropdown}>
      <button className="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
        <span className="most-popular-label">
          {userPreferenceDeltaToString(selectedButton)}
        </span>
        <span className="caret" />
      </button>
      <ul className="dropdown-menu home-most-popular-dropdown">
        {dropdownOptionDeltas.map(delta =>
          <MostPopularDropdowOption
            key={delta}
            delta={delta}
            selectedButton={selectedButton}
            changeDelta={changeDelta}
          />
        )}
      </ul>
    </div>
  );
};