import React from 'react'
import _ from 'lodash'
import {Lib} from '../lib.jsx'

class Api {

  static getEsClient() {

    /**
     * @type {$.es.Client|*}
     */
    return new jQuery.es.Client({
      hosts: 'https://' + bundle.elasticsearch_host
    });
  }

  static getEsIndex() {
    return 'v3/search';
  }

  static getEsType() {
    return 'property';
  }

  static getEsMethod() {
    return 'POST';
  }

  static getAggregationsFields() {
    return {
      "wpp_listing_mls_number": {
        "slug": "mls_id",
        "title": "MLS ID",
        "field": "tax_input.mls_id",
        "search_field": "_search.mls_id",
        "old_key": "mls-id",
        "taxonomy": "wpp_listing"
      },
      "wpp_location_city_state": {
        "slug": "city",
        "title": "City",
        "field": "tax_input.location_city",
        "search_field": "_search.location_city",
        "old_key": "location-city-state",
        "taxonomy": "wpp_location"
      },
      "wpp_location_zip": {
        "slug": "zip",
        "title": "Zip",
        "field": "_system.addressDetail.zipcode",
        "search_field": "_search.location_zip",
        "old_key": "location-zipcode",
        "taxonomy": "wpp_location"
      },
      "wpp_location_county": {
        "slug": "county",
        "title": "County",
        "field": "tax_input.location_county",
        "search_field": "_search.location_county",
        "old_key": "location-county",
        "taxonomy": "wpp_location"
      },
      "wpp_location_subdivision": {
        "slug": "subdivision",
        "title": "Subdivision",
        "field": "tax_input.subdivision",
        "search_field": "_search.subdivision",
        "old_key": "subdivision",
        "taxonomy": "wpp_location"
      },
      "wpp_schools_elementary_school": {
        "slug": "elementary_school",
        "title": "Elementary School",
        "field": "tax_input.elementary_school",
        "search_field": "_search.elementary_school",
        "old_key": "elementary-school",
        "taxonomy": "wpp_schools"
      },
      "wpp_schools_middle_school": {
        "slug": "middle_school",
        "title": "Middle School",
        "field": "tax_input.middle_school",
        "search_field": "_search.middle_school",
        "old_key": "middle-school",
        "taxonomy": "wpp_schools"
      },
      "wpp_schools_high_school": {
        "slug": "high_school",
        "title": "High School",
        "field": "tax_input.high_school",
        "search_field": "_search.high_school",
        "old_key": "high-school",
        "taxonomy": "wpp_schools"
      }
    };
  }

  static getTopAggregations() {
    return {
      "aggs": {
        "wpp_location_city_state_name": {
          "terms": {
            "title": "City",
            "field": "tax_input.wpp_location.wpp_location_city_state.name.raw",
          }
        },
        "wpp_location_city_state_slug": {
          "terms": {
            "title": "City",
            "field": "tax_input.wpp_location.wpp_location_city_state.slug",
          }
        },
        "wpp_location_zip_name": {
          "terms": {
            "title": "Zipcode",
            "field": "tax_input.wpp_location.wpp_location_zip.name.raw",
          }
        },
        "wpp_location_zip_slug": {
          "terms": {
            "title": "Zipcode",
            "field": "tax_input.wpp_location.wpp_location_zip.slug",
          }
        },
        "wpp_location_county_name": {
          "terms": {
            "title": "County",
            "field": "tax_input.wpp_location.wpp_location_county.name.raw"
          }
        },
        "wpp_location_county_slug": {
          "terms": {
            "title": "County",
            "field": "tax_input.wpp_location.wpp_location_county.slug"
          }
        },
        "wpp_location_subdivision_name": {
          "terms": {
            "title": "Subdivision",
            "field": "tax_input.wpp_location.wpp_location_subdivision.name.raw"
          }
        },
        "wpp_location_subdivision_slug": {
          "terms": {
            "title": "Subdivision",
            "field": "tax_input.wpp_location.wpp_location_subdivision.slug"
          }
        }
      }
    };
  }

  static autocompleteQuery(params, callback) {

    let client = Api.getEsClient();

    let rows = [];

    if (!params.term || params.term.length < Lib.MIN_SEARCH_KEY_LENGTH) {
      callback(rows);
      return;
    }

    let aggregationsFields = this.getAggregationsFields();
    let suggest = {
      "post-suggest": {
        "text": params.term,
        "completion": {
          "field": "title_suggest",
          "fuzzy": {
            "fuzziness": 1
          },
          "size": Lib.POST_SUGGEST_COUNT
        }
      }
    };
    for (let i in aggregationsFields) {
      let agg = aggregationsFields[i];

      suggest[i] = {
        "text": params.term,
        "completion": {
          "field": "term_suggest",
          "fuzzy": {
            "fuzziness": Lib.ELASTIC_SEARCH_FUZZINESS_COUNT
          },
          "size": Lib.TERM_SUGGEST_COUNT,
          "contexts": {
            "term_type": [i, _.get(agg, 'old_key', '')]
          }
        }
      };
    }

    let body = {
      suggest
    };

    client.search({
      index: Api.getEsIndex(),
      type: Api.getEsType(),
      method: Api.getEsMethod(),
      size: 0,
      body: body
    }, function selectQueryResponse(err, response) {

      let rows = [];
      for (let aggregationKey in aggregationsFields) {

        let data = null;
        let _buckets = [];

        let suggestResponse = _.get(response, 'suggest');
        for (let i in suggestResponse) {
          let terms = suggestResponse[i];
          if(i === 'post-suggest'){
            continue;
          }

          for(var tInd in terms){

            let term = terms[tInd];

            if (_.get(term, 'options', null) === null) {
              continue;
            }

            for (let ind in term.options) {
              let option = term.options[ind];

              if (_.get(option, '_source.term_type', null) === aggregationKey || _.get(option, '_source.term_type', null) === _.get(aggregationsFields[aggregationKey], 'old_key', null)) {
                _buckets.push({
                  id: _.get(option, '_id', ''),
                  text: _.get(option, '_source.name', ''),
                  term: _.get(option, '_source.slug', ''),
                  count: _.get(option, 'score', ''),
                  taxonomy: _.get(option, '_source.taxonomy', '')
                });

              }
            }
          }
        }

        if (_buckets.length > 0) {
          data = Object.assign({}, data, {
            key: aggregationKey,
            text: aggregationsFields[aggregationKey].title,
            children: _buckets
          });
          rows.push(data);
        }
      }

      let data = null;
      let _buckets = [];

      let postsSuggest = _.get(response, 'suggest.post-suggest');
      for (let c in postsSuggest) {
        let posts = postsSuggest[c];

        if (_.get(posts, 'options', null) === null) {
          continue;
        }

        for (let ind in posts.options) {
          let option = posts.options[ind];

          if (_.get(option, '_source', null) !== null) {

            _buckets.push({
              id: _.get(option, '_source.post_title', ''),
              text: _.get(option, '_source.post_title', ''),
              url: _.get(option, '_source.post_name', null) ? [_.get(wpp, 'instance.settings.configuration.base_slug'), _.get(option, '_source.post_name', null)].join('/') : ''
            });
          }
        }
      }

      if (_buckets.length > 0) {
        data = Object.assign({}, data, {
          key: 'properties',
          text: 'Properties',
          children: _buckets
        });
        rows.push(data);
      }

      callback(rows);
    });
  }

  static topQuery(params, callback) {

    let client = Api.getEsClient();

    let rows = [];

    let aggregations = this.getTopAggregations().aggs;
    let body = {
      "aggs": {}
    };
    for(let aggIndex in aggregations){
      let aggregation = aggregations[aggIndex];

      body.aggs[aggIndex] = {
        "terms": {
          "field": _.get(aggregation, 'terms.field', ''),
          "size": params.size || 0
        }
      }
    }

    client.search({
      index: Api.getEsIndex(),
      type: 'post',
      method: Api.getEsMethod(),
      size: params.size || 0,
      body: body
    }, function selectQueryResponse(err, response) {

      let responseAggs = _.get(response, 'aggregations');

      for (let i in responseAggs) {

        if(i.indexOf('slug') !== -1){
          continue;
        }

        let data = null;
        let _buckets = [];
        let term = responseAggs[i];

        if (_.get(term, 'buckets', null) === null) {
          continue;
        }

        for (let ind in term.buckets) {
          let bucket = term.buckets[ind];

          if (_.get(bucket, 'key', null) !== null) {
            _buckets.push({
              id: _.get(bucket, 'key', ''),
              text: _.get(bucket, 'key', ''),
              term: _.get(responseAggs[_.replace(i, 'name', 'slug')].buckets[ind], 'key', ''),
              count: _.get(bucket, 'doc_count', ''),
              taxonomy: 'wpp_location'
            });

          }
        }

        if (_buckets.length > 0) {
          data = Object.assign({}, data, {
            key: i,
            text: _.get(aggregations[i], 'terms.title'),
            children: _buckets
          });
          rows.push(data);
        }
      }

      callback(rows);
    });
  }

  static createESSearchQuery(params) {
    let terms = {};
    Object.keys(params.term).forEach(tax => {
      terms["terms." + tax + ".slug"] = params.term[tax];
    });

    let query = {
      "bool": {
        "must": [
          {
            "exists": {
              "field": "wpp_location_pin"
            }
          }
        ]
      }
    };

    if (params.locationFilter) {
      // note: the references to topLeft and bottomRight are correct, because of the way ES does its geo_bounding_box
      query.bool.filter = {
        "geo_bounding_box": {
          "wpp_location_pin": {
            "top_left": {
              "lat": params.topLeft.lat,
              "lon": params.topLeft.lon
            },
            "bottom_right": {
              "lat": params.bottomRight.lat,
              "lon": params.bottomRight.lon
            }
          }
        }
      };
    } else {
      if (params.sale_type) {
        query.bool.must.push({
          "term": {
            "terms.wpp_listing_status.slug": 'for-' + params.sale_type.toLowerCase()
          }
        });
      }
      if (params.property_types && params.property_types.length) {
        query.bool.must.push({
          "terms": {
            "terms.wpp_listing_type.slug": params.property_types
          }
        });
      }
      if (params.bedrooms) {
        query.bool.must.push({
          "term": {
            "post_meta.rets_beds": params.bedrooms
          }
        });
      }
      query.bool.must.push({"term": terms});
    }

    query = JSON.stringify(query);

    let size = params.size || 500;
    let from = params.from || 0;

    let aggregations = JSON.stringify({});

    let source = JSON.stringify([
      "post_title",
      "post_name",
      "post_meta.wpp_location_latitude",
      "post_meta.wpp_location_longitude",
      "permalink",
      "post_meta.google_place_id",
      "post_meta.formatted_address",
      "post_meta.formatted_address_simple",
      "post_meta.wpp_location_pin",
      "post_meta.rets_list_date",
      "post_meta.rets_thumbnail_url",
      "terms.wpp_listing_type",
      "post_meta.rets_beds",
      "post_meta.rets_total_baths",
      "post_meta.rets_list_price",
      "post_meta.rets_living_area",
      "post_meta.rets_lot_size_area",
      "post_meta.rets_street_number",
      "post_meta.rets_directions",
      "post_meta.rets_street_name",
      "post_meta.rets_thumbnail_url",
      "wpp_media",
      "tax_input"
    ]);

    // return JSON.parse('{"query":' + query + ',"_source": ' + source + ', "size":' + size + ', "from": ' + from + ', "sort":[{"post_date":{"order":"asc"}},{"post_title":{"order":"asc"}}],"aggregations":' + aggregations + '}');
    return JSON.parse('{"query":' + query + ',"_source": ' + source + ', "size":' + size + ', "from": ' + from + ', "aggregations":' + aggregations + '}');
  }

  static search(query, callback) {

    let client = Api.getEsClient();

    let esQuery = {
      index: Api.getEsIndex(),
      type: 'post',
      method: Api.getEsMethod(),
      body: query,
      size: 18
    };
    client.search(esQuery, function (error, response) {
      callback(response);
    });
  }
}

export default Api;
