import React from 'react';

export const FileUploadMessage = ({ files }) => {
  if (files.filter(file => file.failed).length === 0) {
    return (
      <div className="alert alert-success sidebar-create__link--warning" role="alert">
        Takk for ditt bidrag. Bidraget vil bli behandlet og blir synlig på nettside når den er godkjent.
      </div>
    );
  }


  if (files.filter(file => file.failed).length === files.length) {
    return (<div className="alert alert-danger sidebar-create__link--warning" role="alert">
        Vi kunne dessverre ikke laste opp dine bidrag. Vi beklager problemene. Prøv igjen eller rapporter problemet.
      </div>
    );
  }

  return (
    <div className="alert alert-danger sidebar-create__link--warning" role="alert">
      Noen av bidragene dine ble lastet opp, men ikke alle. Vi beklager problemene. Prøv å laste opp de filene som feilet igjen, eller rapporter problemet.
    </div>
  );
};
