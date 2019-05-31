import React, { Component } from 'react';
import { connect } from 'react-redux';

import { InfoBox } from '../components/info-box';
import {formatThousandNumber, formatNumber, loading } from '../../common/utils';

class InfoBoxesContainer extends Component {

  render() {

    const {
      failed,
      started,
      finished,

      number_files,
      number_downloads,
      number_courses_with_content,
      number_new_elements,
    } = this.props;

    const isLoading = loading(started, finished);

    return (
      <div className="row">
        <InfoBox
          name="downloads"
          icon="fa-download"
          text="Nedlastninger"
          number={number_downloads}
          isLoading={isLoading}
          failed={failed}
          formatter={formatThousandNumber}
        />
        <InfoBox
          name="last-month"
          icon="fa-star"
          text="Bidrag siste mnd"
          number={number_new_elements}
          isLoading={isLoading}
          failed={failed}
          formatter={formatNumber}
        />
        <InfoBox
          name="files"
          icon="fa-archive"
          text="Filer og linker"
          number={number_files}
          isLoading={isLoading}
          failed={failed}
          formatter={formatNumber}
        />
        <InfoBox
          name="courses"
          icon="fa-graduation-cap"
          text="Fag med innhold"
          number={number_courses_with_content}
          isLoading={isLoading}
          failed={failed}
          formatter={formatNumber}
        />
      </div>
    );
  }
}

const mapStateToProps = ({ boxes }) => ({
  failed: boxes.failed,
  started: boxes.started,
  finished: boxes.finished,

  number_files: boxes.number_files,
  number_downloads: boxes.number_downloads,
  number_courses_with_content: boxes.number_courses_with_content,
  number_new_elements: boxes.number_new_elements,
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(InfoBoxesContainer);
