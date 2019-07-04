import React from 'react';

import BoxMostPopularElements from './boxes/most-popular/elements';
import BoxMostPopularCourses from './boxes/most-popular/courses';

import BoxNewestContainer from './boxes/newest-container';
import BoxLastVisitedContainer from './boxes/last-visited-container';
import BoxLastDownloadedContainer from './boxes/last-downloaded-container';

export const MainBoxesContainer = () => (
  <React.Fragment>
    <div className="row">
      <BoxMostPopularElements />
      <BoxMostPopularCourses />
    </div>
    <div className="row">
      <BoxNewestContainer />
      <BoxLastVisitedContainer />
    </div>
    <div className="row">
      <BoxLastDownloadedContainer />
    </div>
  </React.Fragment>
);
