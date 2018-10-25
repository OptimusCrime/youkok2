import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../components/box-wrapper";
import { CourseItem } from "../../../components/course-item";
import { MostPopularDropdown } from "../../../components/most-popular/dropdown";
import {formatNumber, fromDatabaseDateToJavaScriptDate} from "../../../../common/utils";
import { DELTA_POST_POPULAR_ELEMENTS } from "../../../consts";
import {ElementItem} from "../../../components/element-item";
import {ItemTimeAgo} from "../../../components/item-time-ago";

class BoxMostPopularElements extends Component {

  constructor(props) {
    super(props);

    this.state = {
      open: false
    };

    this.toggleDropdown = this.toggleDropdown.bind(this);
  }

  toggleDropdown() {
    this.setState({
      open: !this.state.open
    });
  }

  render() {

    const {
      isLoading,
      elementsMostPopular,
      userPreferences,
    } = this.props;

    const selectedButton = userPreferences[DELTA_POST_POPULAR_ELEMENTS];

    return (
      <BoxWrapper
        title="Mest populære fag"
        titleInline={true}
        isLoading={isLoading}
        isEmpty={!isLoading && elementsMostPopular.length === 0}
        dropdown={
          <MostPopularDropdown
            selectedButton={selectedButton}
            open={this.state.open}
            toggleDropdown={this.toggleDropdown}
          />
        }
      >
        {!isLoading && elementsMostPopular.map((element, index) =>
          <ElementItem element={element} key={index} additional={<span>[{formatNumber(element.downloads)}]</span>} /> )
        }
      </BoxWrapper>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  elementsMostPopular: frontpage.elements_most_popular,
  userPreferences: frontpage.user_preferences,
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxMostPopularElements);