import React from 'react';

export const StencilItemList = ({ size }) => {
  const range = [...Array(size).keys()];

  return (
    <React.Fragment>
      {range.map(key =>
        <li key={key} className="list-group-item">
          <div className={`stencil-item stencil-item__${key}`} />
        </li>
        )}
    </React.Fragment>
  );
};