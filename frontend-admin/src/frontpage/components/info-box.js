import React from 'react';

const InfoBoxWrapper = ({ icon, text, children }) => (
  <div className="col-md-3 col-sm-6 col-xs-12">
    <div className="frontpage-info-box">
      <span className="frontpage-info-box__icon"><i className={`fa ${icon}`}/></span>
      <div className="frontpage-info-box__content">
        <span className="frontpage-info-box__content--text">{text}</span>
        {children}
      </div>
    </div>
  </div>
);

export const InfoBox = ({name, icon, text, number, failed, isLoading, formatter}) => {
  if (failed) {
    return (
      <InfoBoxWrapper
        icon={icon}
        text={text}
      />
    );
  }

  return (
    <InfoBoxWrapper
      icon={icon}
      text={text}
    >
      {isLoading
        ? <span className={`frontpage-info-box__content--number info-box-number-stencil info-box-number-stencil__${name}`}/>
        : <span className="frontpage-info-box__content--number">{formatter(number)}</span>
      }
    </InfoBoxWrapper>
  );
};


