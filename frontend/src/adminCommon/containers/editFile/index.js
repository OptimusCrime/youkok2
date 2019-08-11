import React from 'react';
import {Modal} from "../../components/modal";
import {ModalTitle} from "../../components/modal/modal-title";
import {ModalBody} from "../../components/modal/modal-body";
import {ModalFooter} from "../../components/modal/modal-footer";
import {connect} from "react-redux";
import {hideEditFileModal} from "../../redux/editFile/actions";

const EditFileModal = props => {

  const {
    title,
  } = props;

  return (
    <Modal>
      <ModalTitle
        onClose={props.hideEditFileModal}
        title={title}
      />
      <ModalBody>
        <div className="form-group">
          <p>Her kommer det masse greier</p>
        </div>
      </ModalBody>
      <ModalFooter>
        <button
          type="button"
          className="btn btn-default"
          onClick={props.hideEditFileModal}
        >
          Lukk
        </button>
        <button
          type="button"
          className="btn btn-primary"
          onClick={() => console.log('Click på knapp')}
        >
          Lagre
        </button>
      </ModalFooter>
    </Modal>
  );
};

const mapStateToProps = ({editFile}) => ({
});

const mapDispatchToProps = {
  hideEditFileModal,
};

export default connect(mapStateToProps, mapDispatchToProps)(EditFileModal);
