import React from 'react';

export const Box = ({children, content, box_title_class = ""}) => {

  return (
    <div className="box">
      <div className="box-header with-border">
        <h2 className={`box-title ${box_title_class}`}>
          {children}
        </h2>
      </div>
      <div className="body-box">
        <div className="files-listing">
          {content}
        </div>
      </div>
    </div>
  );
};

