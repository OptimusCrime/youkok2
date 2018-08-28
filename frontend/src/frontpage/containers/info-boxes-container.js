import React, { Component } from 'react';
import { connect } from 'react-redux';

import { InfoBox } from '../components/info-box';
import { loading } from '../utilities/utils';

class InfoBoxesContainer extends Component {

  render() {

    const {
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
          icon="fa-download"
          text="Nedlastninger"
          number={number_downloads}
          isLoading={isLoading}
        />
        <InfoBox
          icon="fa-star"
          text="Bidrag siste mnd"
          number={number_new_elements}
          isLoading={isLoading}
        />
        <InfoBox
          icon="fa-archive"
          text="Filer og linker"
          number={number_files}
          isLoading={isLoading}
        />
        <InfoBox
          icon="fa-graduation-cap"
          text="Fag med innhold"
          number={number_courses_with_content}
          isLoading={isLoading}
        />
      </div>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
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