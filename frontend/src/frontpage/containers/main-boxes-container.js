import React, { Component } from 'react';
import { connect } from 'react-redux';

import BoxMostPopularCourses from './boxes/most-popular/courses';
import BoxMostPopularElements from './boxes/most-popular/elements';

import BoxLatestElements from './boxes/latest-elements';
import BoxLastVisitedContainer from './boxes/last-visited-container';
import BoxLastDownloadedContainer from './boxes/last-downloaded-container';

import { loading } from '../../common/utils';

class InfoBoxesContainer extends Component {

  render() {

    const {
      failed,
      started,
      finished,
    } = this.props;

    const isLoading = loading(started, finished);

    return (
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
          <BoxLastDownloadedContainer failed={failed} isLoading={isLoading} />
        </div>
      </React.Fragment>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  failed: frontpage.failed,
  started: frontpage.started,
  finished: frontpage.finished,

  number_files: frontpage.info.number_files,
  number_downloads: frontpage.info.number_downloads,
  number_courses_with_content: frontpage.info.number_courses_with_content,
  number_new_elements: frontpage.info.number_new_elements,
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(InfoBoxesContainer);
