import React from 'react';
import {formatNumber} from "../../common/utils";

export const Block = ({ background, icon, text, value, isLoading}) => (
  <div className="col-md-3 col-sm-6 col-xs-12">
    <div className="info-box">
      <span className={`info-box-icon bg-${background}`}><i className={`fa fa-${icon}`}/></span>
      <div className="info-box-content">
        <span className="info-box-text">{text}</span>
        <span className="info-box-number">{isLoading ? 'Laster...' : formatNumber(value)}</span>
      </div>
    </div>
  </div>
);
