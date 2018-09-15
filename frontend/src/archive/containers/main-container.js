import React, { Component } from 'react';
import { connect } from 'react-redux';

import ArchiveContainer from './archive-container';

import { fetchArchive } from '../redux/archive/actions';

const BreadcrumbElement = ({ parent, index, numParents }) => {
  if ((index + 1) === numParents) {
    return <li>{parent.name || parent.courseCode}</li>;
  }

  return <li><a href={parent.url}>{parent.name || parent.courseCode}</a></li>;
};

class MainContainer extends Component {

  componentDidMount() {
    this.props.fetchArchive();
  }

  render() {

    const {
      failed
    } = this.props;

    if (failed) {
      return (
        <p>Vi har visst litt tekniske problemer her...</p>
      );
    }

    const numParents = SITE_DATA.archive_parents.length;

    return (
      <React.Fragment>
        <div className="row">
          <div className="col-xs-12">
            <ol className="breadcrumb" id="archive-breadcrumbs">
              <li><a href={SITE_DATA.archive_url_frontpage}>Hjem</a></li>
              <li><a href={SITE_DATA.archive_url_courses}>Emner</a></li>
              {SITE_DATA.archive_parents.map((parent, index) =>
                <BreadcrumbElement
                  key={index}
                  parent={parent}
                  index={index}
                  numParents={numParents}
                />
              )}
            </ol>
          </div>
        </div>
        <div className="row">
          <div className="col-xs-12" id="archive-title">
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
  }
}

const mapStateToProps = ({ archive }) => ({
  failed: archive.failed,
});

const mapDispatchToProps = {
  fetchArchive
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);