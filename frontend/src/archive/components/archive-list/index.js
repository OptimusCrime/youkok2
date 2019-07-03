import React from "react";

export const ArchiveList = ({ children }) => (
  <div className="archive-list">
    <div className="archive-row archive-row__header">
      <div className="archive-row__icon">
      </div>
      <div className="archive-row__name">
        <strong>Navn</strong>
      </div>
      <div className="archive-row__downloads">
        <strong>Nedlastninger</strong>
      </div>
      <div className="archive-row__age">
        <strong>Postet</strong>
      </div>
    </div>
    {children}
  </div>
);
