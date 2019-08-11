import React from 'react';
import {connect} from 'react-redux';

import FileListingContainer from '../fileListingContent';
import {FileListingBox} from "../fileListingBox";
import {showCreateDirectoryModal} from "../../redux/createDirectory/actions";
import {showEditFileModal} from "../../redux/editFile/actions";

const FileListingCourseContainer = props => {

  const {content, disabled} = props;

  return (
    <FileListingBox
      box_title_class="box-title-links"
      disabled={disabled}
      content={
        <FileListingContainer
          course={content.id}
          files={content.children}
        />
      }
    >
      <React.Fragment>
        <a
          href="#"
          onClick={e => {
            e.preventDefault();
            props.showEditFileModal(
              content.id,
              content.id
            )
          }}
        >
          {content.courseName}&nbsp;&mdash;&nbsp;{content.courseCode}
        </a>
        &nbsp;
        <i
          className="fa fa-plus-square"
          onClick={
            () => props.showCreateDirectoryModal(
              content.id,
              content.id,
              `${content.courseCode}: ${content.courseName}`
            )
          }
        />
        &nbsp;
        <a
          href={content.url}
          target="_blank"
        >
          <i className="fa fa-external-link"/>
        </a>
      </React.Fragment>
    </FileListingBox>
  );
};

const mapDispatchToProps = {
  showCreateDirectoryModal,
  showEditFileModal
};

export default connect(() => ({}), mapDispatchToProps)(FileListingCourseContainer);
