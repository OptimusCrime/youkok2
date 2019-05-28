import React from 'react';
import {ORDER_ASC, ORDER_DESC} from "../constants";

export const HeaderColumn = ({id, text, column, order, changeOrder }) => (
  <div
    className={`course-row__${id} course-row__header--${id}`}
    onClick={() => {
      if (column === id) {
        changeOrder(
          id,
          order === ORDER_DESC ? ORDER_ASC : ORDER_DESC
        );
      }
      else {
        changeOrder(id, ORDER_DESC);
      }
    }}
  >
    <strong>{text}</strong>
    {column === id
      ? <i className={`fa fa-sort-${order}`} aria-hidden="true"/>
      : <i className="fa fa-sort inactive" aria-hidden="true"/>
    }
  </div>
);
