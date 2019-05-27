import React from 'react';
import {ORDER_ASC, ORDER_DESC} from "../constants";

export const HeaderColumn = ({id, text, sortColumn, sortOrder, changeOrder }) => (
  <div
    className={`course-row__${id} course-row__header--${id}`}
    onClick={() => {
      if (sortColumn === id) {
        changeOrder(
          id,
          sortOrder === ORDER_DESC ? ORDER_ASC : ORDER_DESC
        );
      }
      else {
        changeOrder(id, ORDER_DESC);
      }
    }}
  >
    <strong>{text}</strong>
    {sortColumn === id
      ? <i className={`fa fa-sort-${sortOrder}`} aria-hidden="true"/>
      : <i className="fa fa-sort inactive" aria-hidden="true"/>
    }
  </div>
);
