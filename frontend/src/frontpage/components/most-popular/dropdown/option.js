import React from "react";

import { userPreferenceDeltaToString } from "../../../utilities/utils";

export const MostPopularDropdowOption = ({ selectedButton, changeDelta, delta }) => (
  <li
    className={selectedButton === delta ? 'disabled' : ''}
    onClick={e => {
      e.preventDefault();

      if (selectedButton === delta) {
        return false;
      }

      changeDelta(delta);
    }}
  >
    <a href="#">{userPreferenceDeltaToString(delta)}</a>
  </li>
);