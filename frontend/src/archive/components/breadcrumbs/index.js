import React from "react";

const BreadcrumbElement = ({ parent, index, numParents }) => {
  if ((index + 1) === numParents) {
    return <li>{parent.name || parent.courseCode}</li>;
  }

  return <li><a href={parent.url}>{parent.name || parent.courseCode}</a></li>;
};

export const Breadcrumbs = () => (
  <ol className="breadcrumb">
    <li><a href={SITE_DATA.archive_url_frontpage}>Hjem</a></li>
    <li><a href={SITE_DATA.archive_url_courses}>Emner</a></li>
    {SITE_DATA.archive_parents.map((parent, index) =>
      <BreadcrumbElement
        key={index}
        parent={parent}
        index={index}
        numParents={SITE_DATA.archive_parents.length}
      />
    )}
  </ol>
);
