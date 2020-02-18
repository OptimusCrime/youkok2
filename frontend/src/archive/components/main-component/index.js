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
    {window.SITE_DATA.archive_requested_deletion &&
      <div className="row">
        <div className="col-xs-12 archive-title">
          <div className="alert alert-warning" role="alert">
            Bidrag knyttet til dette faget er fjernet etter forespørsel. Det kan derfor hende at innholdet du leter etter ikke lenger er tilgjengelig.
          </div>
        </div>
      </div>
    }
    <div className="row">
      <div className="col-xs-12 archive-title">
        <h1>{window.SITE_DATA.archive_title}</h1>
        {window.SITE_DATA.archive_sub_title === null ? '' :
          <React.Fragment>
            &nbsp;
            <span>&ndash;</span>
            &nbsp;
            <h2>{window.SITE_DATA.archive_sub_title}</h2>
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
