import React from "react";

export const ArchiveWrapper = ({ children }) => (
  <div className="archive-list">
    <div className="archive-row archive-row--header">
      <div className="archive-row-icon">
      </div>
      <div className="archive-row-name">
        <strong>Navn</strong>
      </div>
      <div className="archive-row-downloads">
        <strong>Nedlastninger</strong>
      </div>
      <div className="archive-row-age">
        <strong>Postet</strong>
      </div>
    </div>
    {children}
  </div>
);
