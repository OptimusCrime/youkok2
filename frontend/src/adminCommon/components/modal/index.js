import React from 'react';

export const Modal = ({children}) => (
  <div
    className="modal fade in"
    tabIndex="-1"
    role="dialog"
    style={{
      display: 'block',
      paddingRight: '15px'
    }}
  >
    <div className="modal-dialog" role="document">
      <div className="modal-content">
        {children}
      </div>
    </div>
  </div>
);
