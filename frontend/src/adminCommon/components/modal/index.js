import React from 'react';

export const Modal = ({children, onClose}) => (
  <div
    className="modal fade in"
    tabIndex="-1"
    role="dialog"
    style={{
      display: 'block',
      paddingRight: '15px'
    }}
    onClick={e => {
      if (e.target === e.currentTarget) {
        onClose();
      }
    }}
  >
    <div className="modal-dialog" role="document">
      <div className="modal-content">
        {children}
      </div>
    </div>
  </div>
);
