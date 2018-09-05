import React from 'react';
import {randomBetween} from "../../../common/utils";

export const StencilArchiveList = ({ size }) => {
  const range = [...Array(size).keys()];

  return (
    <React.Fragment>
      {range.map(key =>
        <div key={key} className="archive-row">
          <div className="archive-row-icon">
            <div className="stecil-archive-row stencil-archive-icon" />
          </div>
          <div className="archive-row-name">
            <div className={`stecil-archive-row stencil-archive-name stencil-archive-name__${randomBetween(0, size)}`} />
          </div>
          <div className="archive-row-downloads">
            <div className={`stecil-archive-row stencil-archive-downloads stencil-archive-downloads__${randomBetween(0, size)}`} />
          </div>
          <div className="archive-row-age">
            <div className="stecil-archive-row stencil-archive-age" />
          </div>
        </div>
      )}
    </React.Fragment>
  );
};