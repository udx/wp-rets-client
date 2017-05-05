import React, {Component, PropTypes} from 'react';
import LoadingCircle from '../LoadingCircle.jsx';
import {Lib} from '../../lib.jsx';
import _ from 'lodash';
import Waypoint from 'react-waypoint';
import PropertyCard from '../PropertyCard.jsx';

class SearchResultListing extends Component {
  static propTypes = {
    allowPagination: PropTypes.bool.isRequired,
    properties: PropTypes.array.isRequired,
    seeMoreHandler: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.state = {loading: false};
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.properties !== this.props.properties) {
      console.log('resetting loading');
      this.setState({loading: false});
    }
  }

  render() {

    let properties = _.get(this.props, 'properties', []);

    return (
      <div className={Lib.THEME_CLASSES_PREFIX + "listing-wrap"} ref={(r) => this.listingWrapElement = r}>
        <div className="container-fluid">
          <div className="row">
            {
              properties.map((p, i) => {

                    let city = '';
                    let zipCode = '';

                    let listingLocation = _.get(p, '_source.tax_input.wpp_listing_location', []);

                    for (let termInd in listingLocation) {
                      let term = listingLocation[termInd][0];

                      switch (term.term_type) {
                        case 'location-city-state':
                          city = term.name;
                          break;
                        case 'location-zipcode':
                          zipCode = term.name;
                          break;
                      }
                    }

                    let item = {
                      address: _.get(p, '_source.post_meta.rets_address', ''),
                      location: _.get(p, '_source.post_meta.wpp_location_pin', []),
                      baths: _.get(p, '_source.post_meta.rets_total_baths', 0),
                      beds: _.get(p, '_source.post_meta.rets_beds', 0),
                      full_address: _.get(p, '_source.post_meta.formatted_address_simple', ''),
                      gallery_images: _.get(p, '_source.wpp_media', []).map((media) => media.url),
                      living_area: _.get(p, '_source.post_meta.rets_living_area', ''),
                      price: _.get(p, '_source.post_meta.rets_list_price[0]', 0),
                      relative_permalink: [_.get(wpp, 'instance.settings.configuration.base_slug'), _.get(p, '_source.post_name', '')].join(Lib.URL_DELIMITER),
                      thumbnail: _.get(p, '_source.post_meta.rets_thumbnail_url', '')
                    };

                    return (
                      <div className="col-lg-6 col-xl-4">
                        <PropertyCard data={item} listType={Lib.PROPERTIES_LIST_DEFAULT} key={i}/>
                      </div>
                    );
                }
              )}
          </div>
        </div>
        {this.props.allowPagination ?
          <div className={Lib.THEME_CLASSES_PREFIX + "search-result-container"}>
            <div className={Lib.THEME_CLASSES_PREFIX + "search-result-inner-container"}>
              {this.state.loading ?
                <LoadingCircle />
                : null}
              <p>Showing {this.props.properties.length} results</p>
              <Waypoint
                onEnter={() => {
                  this.setState({loading: true});
                  this.props.seeMoreHandler();
                }}
              />
            </div>
          </div>
          : null}
      </div>
    );
  }
}

export default SearchResultListing;
