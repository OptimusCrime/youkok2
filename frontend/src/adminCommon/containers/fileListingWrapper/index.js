import React from 'react';
import {connect} from 'react-redux';

import {loading} from "../../../common/utils";
import CreateDirectoryModal from "../createDirectory";
import EditFileModal from "../editFile";

const FileListingWrapper = ({ started, finished, failed, children, showCreateDirectoryModal, showEditFileModal }) => {

  if (failed) {
    return (
      <p>Her gikk visst noe galt...</p>
    );
  }

  const isLoading = loading(started, finished);

  if (isLoading) {
    return (
      <div className="admin-files-loading">
        <i className="fa fa-cog fa-spin fa-3x fa-fw"/>
        <p>Laster...</p>
      </div>
    );
  }

  return (
    <React.Fragment>
      {showCreateDirectoryModal && <CreateDirectoryModal/>}
      {showEditFileModal && <EditFileModal/>}
      {children}
    </React.Fragment>
  );
};

const mapStateToProps = ({files, createDirectory, editFile}) => ({
  started: files.started,
  finished: files.finished,
  failed: files.failed,
  showCreateDirectoryModal: createDirectory.showModal,
  showEditFileModal: editFile.showModal
});

export default connect(mapStateToProps, {})(FileListingWrapper);
