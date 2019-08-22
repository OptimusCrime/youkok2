import React from 'react';

export const ModalTitle = ({ title, onClose}) => (
    <div className="modal-header">
      <button
        type="button"
        className="close"
        aria-label="Close"
        onClick={onClose}
      ><span aria-hidden="true">×</span>
      </button>
      <h4 className="modal-title">{title}</h4>
    </div>
);
