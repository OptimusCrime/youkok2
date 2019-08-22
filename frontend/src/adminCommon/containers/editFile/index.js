import React from 'react';
import {Modal} from "../../components/modal";
import {ModalTitle} from "../../components/modal/modal-title";
import {ModalBody} from "../../components/modal/modal-body";
import EditFileData from './edit-file-data';
import {connect} from "react-redux";
import {hideEditFileModal} from "../../redux/editFile/actions";

const EditFileModal = props => {

  const {
    fetchStarted,
    fetchFailed,
  } = props;

  if (fetchStarted) {
    return (
      <Modal
        onClose={props.hideEditFileModal}
      >
        <ModalTitle
          onClose={props.hideEditFileModal}
          title="Laster..."
        />
        <ModalBody>
          <p>Vennligst vent...</p>
        </ModalBody>
      </Modal>
    );
  }

  if (fetchFailed) {
    return (
      <Modal
        onClose={props.hideEditFileModal}
      >
        <ModalTitle
          onClose={props.hideEditFileModal}
          title="Noe gikk galt"
        />
        <ModalBody>
          <p>Kunne ikke laste element fra databasen.</p>
        </ModalBody>
      </Modal>
    );
  }

  return <EditFileData/>;
};

const mapStateToProps = ({editFile}) => ({
  fetchStarted: editFile.fetchStarted,
});

const mapDispatchToProps = {
  hideEditFileModal,
};

export default connect(mapStateToProps, mapDispatchToProps)(EditFileModal);
