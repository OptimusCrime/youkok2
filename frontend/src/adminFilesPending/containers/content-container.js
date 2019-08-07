import React from 'react';
import {connect} from 'react-redux';

import FilesListingContainer from './files-listing-container';
import {Box} from "../components/box";

const ContentContainer = ({content}) => {

  return (
    <Box
      box_title_class="box-title-links"
      content={
        <FilesListingContainer
          files={content.children}
        />
      }
    >
      <React.Fragment>
        <a
          href="#"
          onClick={() => console.log('click on title')}
        >
          {content.courseName}&nbsp;&mdash;&nbsp;{content.courseCode}
        </a>
        &nbsp;
        <i
          className="fa fa-plus-square"
          onClick={() => console.log('click on plus')}
        />
        &nbsp;
        <a
          href={content.url}
          target="_blank"
        >
          <i className="fa fa-external-link"/>
        </a>
      </React.Fragment>
    </Box>
  );
};


export default connect(() => ({}), {})(ContentContainer);
