import React from 'react';

import ArchiveContainer from '../../containers/archive-container';
import {Breadcrumbs} from "../breadcrumbs";

export const MainComponent = () => (
  <React.Fragment>
    <div className="row">
      <div className="col-xs-12">
        <Breadcrumbs />
      </div>
    </div>
    <div className="row">
      <div className="col-xs-12 archive-title">
        <h1>{SITE_DATA.archive_title}</h1>
        {SITE_DATA.archive_sub_title === null ? '' :
          <React.Fragment>
            &nbsp;
            <span>&ndash;</span>
            &nbsp;
            <h2>{SITE_DATA.archive_sub_title}</h2>
          </React.Fragment>
        }
      </div>
    </div>
    <div className="row">
      <div className="col-xs-12">
        <ArchiveContainer />
      </div>
    </div>
  </React.Fragment>
);
