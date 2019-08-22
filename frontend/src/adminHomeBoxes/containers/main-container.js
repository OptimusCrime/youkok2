import React from 'react';
import {connect} from 'react-redux';
import {loading} from "../../common/utils";
import {Block} from "../components/block";

const AdminHomeBoxes = ({started, finished, failed, data}) => {

  if (failed) {
    return (
      <p>Woops</p>
    );
  }

  const isLoading = loading(started, finished);

  const {
    sessions_week_num,
    files_num,
    downloads_num,
    courses_num,
  } = data;

  return (
    <React.Fragment>
      <Block
        background="aqua"
        icon="download"
        text="Nedlastninger"
        value={downloads_num}
        isLoading={isLoading}
      />
      <Block
        background="red"
        icon="users"
        text="Sesjoner siste uke"
        value={sessions_week_num}
        isLoading={isLoading}
      />
      <Block
        background="green"
        icon="files-o"
        text="Filer og linker"
        value={files_num}
        isLoading={isLoading}
      />
      <Block
        background="yellow"
        icon="graduation-cap"
        text="Fag"
        value={courses_num}
        isLoading={isLoading}
      />
    </React.Fragment>
  );
};

const mapStateToProps = ({boxes}) => ({
  started: boxes.started,
  finished: boxes.finished,
  failed: boxes.failed,
  data: boxes.data,
});

export default connect(mapStateToProps)(AdminHomeBoxes);
