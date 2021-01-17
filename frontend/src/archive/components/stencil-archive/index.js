import React from 'react';
import {randomBetween} from "../../../common/utils";

export const StencilArchiveTitle = () => <div className="stencil-archive__title"/>;

export const StencilArchiveList = ({ size }) => {
  const range = [...Array(size).keys()];

  return (
    <React.Fragment>
      {range.map(key =>
        <div key={key} className="archive-row">
          <div className="archive-row__icon">
            <div className="stencil-archive__row stencil-archive__icon" />
          </div>
          <div className="archive-row__name">
            <div className={`stencil-archive__row stencil-archive__name stencil_archive__name--${randomBetween(0, size)}`} />
          </div>
          <div className="archive-row__downloads">
            <div className={`stencil-archive__row stencil-archive__downloads stencil-archive__downloads--${randomBetween(0, size)}`} />
          </div>
          <div className="archive-row__age">
            <div className="stencil-archive__row stencil-archive__age" />
          </div>
        </div>
      )}
    </React.Fragment>
  );
};
