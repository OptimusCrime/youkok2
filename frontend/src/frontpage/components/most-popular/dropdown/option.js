import React from "react";

import { userPreferenceDeltaToString } from "../../../utilities/utils";

export const MostPopularDropdowOption = ({ selectedButton, delta }) => (
  <li
    className={selectedButton === delta ? 'disabled' : ''}
    onClick={e => {
      e.preventDefault();

      if (selectedButton === delta) {
        return false;
      }

      console.log('Update here')
    }}
  >
    <a data-delta="3" href="#">{userPreferenceDeltaToString(delta)}</a>
  </li>
);