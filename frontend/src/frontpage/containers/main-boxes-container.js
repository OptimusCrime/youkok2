import React from 'react';

//import BoxMostPopularCourses from './boxes/most-popular/courses';
//import BoxMostPopularElements from './boxes/most-popular/elements';

import BoxNewestContainer from './boxes/newest-container';
import BoxLastVisitedContainer from './boxes/last-visited-container';
import BoxLastDownloadedContainer from './boxes/last-downloaded-container';

/*
    <React.Fragment>
        <div className="row">
          <BoxMostPopularCourses failed={failed} isLoading={isLoading} />
          <BoxMostPopularElements failed={failed} isLoading={isLoading} />
        </div>
        <div className="row">
          <BoxLatestElements failed={failed} isLoading={isLoading} />
          <BoxLastVisitedContainer failed={failed} isLoading={isLoading} />
        </div>
        <div className="row">
          <BoxLastDownloadedContainer />
        </div>
      </React.Fragment>
     */


export const MainBoxesContainer = () => (
  <React.Fragment>
    <div className="row">
      <BoxNewestContainer />
      <BoxLastVisitedContainer />
    </div>
    <div className="row">
      <BoxLastDownloadedContainer />
    </div>
  </React.Fragment>
);
