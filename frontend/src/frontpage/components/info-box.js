import React from 'react';

export const InfoBox = ({ icon, text, number, isLoading, formatter }) => (
  <div className="col-md-3 col-sm-6 col-xs-12">
    <div className="frontpage-info-box">
      <span className="info-box-icon"><i className={`fa ${icon}`}/></span>
      <div className="info-box-content">
        <span className="info-box-text">{text}</span>
        <span className="info-box-number">{!isLoading && formatter(number)}</span>
      </div>
    </div>
  </div>
);