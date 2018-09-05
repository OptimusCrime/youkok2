import React from 'react';
import {randomBetween} from "../../../common/utils";

export const StencilItemList = ({ size }) => {
  const range = [...Array(size).keys()];

  return (
    <React.Fragment>
      {range.map(key =>
        <li key={key} className="list-group-item">
          <div className={`stencil-item stencil-item__${randomBetween(0, size)}`} />
        </li>
        )}
    </React.Fragment>
  );
};