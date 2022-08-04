import React, {Component} from "react";
import { geoCentroid } from "d3-geo";
import {
  ComposableMap,
  Geographies,
  Geography,
  Marker,
  Annotation
} from "react-simple-maps";
import Select from 'react-select';

import allStates from "./data/allstates.json";

const geoUrl = "http://nvtac.debugme.in/wp-content/themes/twentysixteensubtheme2/map/src/data/states-10m.json";

const offsets = {
  VT: [50, -8],
  NH: [34, 2],
  MA: [30, -1],
  RI: [28, 2],
  CT: [35, 10],
  NJ: [34, 1],
  DE: [33, 0],
  MD: [47, 10],
  DC: [49, 21]
};

class MapChart extends Component {

  constructor(props) {
    super(props);
    this.state = { grantees: '' };
    this.state = { countylist: '' };
    this.state = { ziplist: '' };
    this.state = { county: '', statevalue: '', zip: '' };
    this.handleClick = this.handleClick.bind(this);
    this.handleCountyChange = this.handleCountyChange.bind(this);
    this.handleZipChange = this.handleZipChange.bind(this);
    this.getGrantees = this.getGrantees.bind(this);
    this.getZipcodes = this.getZipcodes.bind(this);
    this.getGrantees();
    this.getZipcodes();
  }

  handleClick = async (geography) => {
    await this.setState({statevalue: geography.currentTarget.getAttribute("data-id"), county: '', zip: ''})
    this.getGrantees();
    this.getZipcodes();
  }

  handleCountyChange = async (selectedOption) => {
    await this.setState({county: selectedOption})
    this.getGrantees();
    this.getZipcodes();
  }

  handleZipChange = async (selectedOption) => {
    await this.setState({zip: selectedOption})
    this.getGrantees();
  }

  async getGrantees() {
    var state = this.state.statevalue;
    var county = this.state.county;
    var zip = this.state.zip;
    var county_val = '';
    if (county.value) {
      county_val = county.value;
    }

    var zip_val = '';
    if (zip.value) {
      zip_val = zip.value;
    }

    let response = await fetch(`https://nvtac.org/wp-json/grantee/v1/map?state=${state}&county=${county_val}&zip=${zip_val}`)
        .then(response => {
          return response.json();
        })
        .then((responseData) => {
          this.setState({ grantees: responseData });
          this.setState({ countylist: responseData });
        })
        .catch(error => {
          console.log(error);
        });

  }

  async getZipcodes() {
    var state = this.state.statevalue;
    var county = this.state.county;
    var zip = this.state.zip;
    var county_val = '';
    if (county.value) {
      county_val = county.value;
    }

    var zip_val = '';
    if (zip.value) {
      zip_val = zip.value;
    }

    let response_zip = await fetch(`https://nvtac.org/wp-json/grantee/v1/zip?state=${state}&county=${county_val}&zip=${zip_val}`)
        .then(response_zip => {
          return response_zip.json();
        })
        .then((responseData) => {
          this.setState({ ziplist: responseData });
        })
        .catch(error => {
          console.log(error);
        });

  }

  render () {
    var items = '';
    var countyitems = [];
    var zipitems = [];
    if (this.state.grantees) {
      const mapitems = JSON.parse(this.state.grantees);
      items = mapitems.map(function(row, i) {
        var serve_counties = row.service_delivery_area.split("\n");
        var countiestext = serve_counties.map(function(servelist, a) {
          var countiesserve = servelist.split(":");
          return (
              <p><strong>{countiesserve[0]}: </strong>{countiesserve[1]}</p>
          )
        })
        return <div className="tcontainer">
          <div className="container">
            <div className="item1"><h4>{row.grantee_name}</h4>
              <div className="description">
                <p><strong>Main Office Address: </strong>{row.street1 !=='' && `${row.street1},`} {row.street2 !=='' && `${row.street2},`} {row.city}, {row.state}, {row.zip}</p>
                <p><strong>Website: <a href={row.website}>{row.website}</a></strong></p>
                <p><strong>Counties or Independent Cities Served:</strong></p>
                {countiestext}
              </div>
            </div>
          </div>
        </div>
      })
    }

    if (this.state.countylist) {
      const countylist = JSON.parse(this.state.countylist);
      var selectedcounty = [];
      countylist.map(function(row, i) {
        var countieslist = row.service_delivery_area.split("\n");
        countieslist.map(function(countiesitems, a) {
          var counties = countiesitems.split(",");
          counties.map(function(items, y) {
            if (items.includes(':')) {
              var split = items.split(":");
              items = split[1];
            }
            var item = items.trim();
            if (!selectedcounty.includes(item)) {
              selectedcounty.push(item);
              countyitems.push({value: item, label: item})
            }
          })
        })
      })
    }

    if (this.state.ziplist) {
      const ziplist = JSON.parse(this.state.ziplist);
      const selectedzip = this.state.zip;
      ziplist.map(function(row, i) {
        if (row.zip) {
          zipitems.push({value: row.zip, label: row.zip})
        }
      })
    }

    return (
        <div className="map-container entry-content">
        <ComposableMap projection="geoAlbersUsa">
          <Geographies geography={geoUrl}>
            {({geographies}) => (
                <>
                  {geographies.map(geo => {
                    const cur = allStates.find(s => s.val === geo.id);
                    var fillcolor = '#44a6da';
                    if (cur.id === this.state.statevalue) {
                      fillcolor = '#2f5083';
                    }
                    if (cur.id === 'GU') {
                      geo.svgPath = "m280.4,520.36908l2.5625,0.61011l2.5625,-0.12201l0.48813,2.5625l0.97617,0.36609l0.36606,-1.95238l-0.4881,-1.5863l1.09824,0.73212l0.24402,-1.83032l1.70834,-1.70837l4.6369,-1.46429l3.29462,0.24408l1.46429,-1.22028l-0.61012,-1.70831l2.19644,-0.61011l1.46429,-0.12201l0.36607,-3.29469l2.92859,-4.75891l1.22025,-2.44043l0.12199,-4.27086l2.31847,-3.29468l1.95233,0.24408l4.02682,5.00299l0.9762,0.97619l5.125,0.61011l-0.73215,2.80652l-1.46429,2.07446l-0.24405,2.44043l-1.5863,2.44049l-4.02675,1.95239l0,0.9762l-4.02679,3.66071l-4.02679,3.29462l-1.5863,0.85419l-0.36604,1.09821l-2.92865,0.36609l0.61015,1.22021l-1.95236,2.5625l-0.12201,6.46729l-0.85419,0.61012l0.9762,0.9762l0.12204,3.66071l-1.95239,2.5625l-2.19643,2.31847l-1.70834,1.70831l-2.80652,0l-2.80655,-1.34229l-2.44049,-4.39282l-0.97617,-4.75897l-1.83038,-1.34222l1.95238,-2.5625l1.22021,-4.1488l-1.46426,-2.5625l-2.19644,-2.44055l-1.5863,-1.46423l-0.00003,-0.00006l-0.00001,0z";
                    }
                    if (cur.id === 'PR') {
                      geo.svgPath = "m710.0833,555.15714c0,0 1.9643,-0.9867 2.9643,-0.9867c0,0 1.4552,-0.8521 1.5091,-0.8521c0.0539,0 0.9162,-0.3588 0.9162,-0.3588c0,0 0,-1.4352 0,-1.4352c0,0 -0.5929,-0.8522 -0.5929,-0.8522c0,0 0.3773,-0.7624 0.3773,-0.7624c0,0 0.7007,-0.3588 0.7007,-0.3588c0,0 0.9701,-0.583 0.9701,-0.583c0,0 1.9941,-0.1346 2.048,-0.1346c0.0539,0 3.2337,0.2691 3.2337,0.2691c0,0 1.8325,0.6727 1.8325,0.6727c0,0 1.1857,0.1794 1.1857,0.1794c0,0 1.61681,0.0449 1.61681,0.0449c0,0 1.02409,0.2242 1.02409,0.2242c0,0 1.7246,-0.3588 1.7246,-0.3588c0,0 1.2935,0.1346 1.4015,0.1346c0.10799,0 1.994,-0.1346 1.994,-0.1346c0,0 3.341,0.8073 3.341,0.8073c0,0 1.779,-0.3139 1.779,-0.3139c0,0 1.347,-0.4485 1.347,-0.4485c0,0 1.509,-0.0897 1.563,-0.0897c0.054,0 2.048,0.3139 2.10201,0.3139c0.054,0 0.863,0 0.863,0c0,0 0.862,-0.0897 0.862,-0.0897c0,0 2.533,0.5382 2.587,0.5382c0.054,0 1.886,0.1794 1.886,0.1794c0,0 0.755,-0.3588 0.755,-0.3588c0,0 0.32301,-0.4036 0.32301,-0.4485c0,-0.0448 1.45499,-0.0448 1.509,-0.0448c0.054,0 3.234,0.2242 3.234,0.2242c0,0 0.701,0.3588 0.701,0.3588c0,0 1.239,0.0897 1.239,0.0897c0,0 0.701,0.1794 0.701,0.1794c0,0 0.701,-0.5382 0.701,-0.5382c0,0 0.808,0.314 0.808,0.314c0,0 0.431,0.2691 0.431,0.2691c0,0 2.425,0 2.425,0c0,0 0.647,-0.2243 0.647,-0.2691c0,-0.0449 -0.323,0.583 -0.323,0.583c0,0 0.916,0.4934 0.916,0.4934c0,0 1.29399,-0.8073 1.29399,-0.8073c0,0 0,0.7176 0,0.7176c0,0 1.347,0.2691 1.347,0.2691c0,0 0.054,0.4933 0.054,0.4933c0,0 1.18501,-0.4036 1.18501,-0.4036c0,0 -0.916,-0.8522 -0.916,-0.8522c0,0 1.23999,0.0449 1.23999,0.0449c0,0 1.886,0.2691 1.886,0.2691c0,0 0.755,0.3588 0.755,0.3588c0,0 1.024,0 1.024,0c0,0 0.64699,-0.7176 0.64699,-0.7176c0,0 2.425,0.4933 2.425,0.5382c0,0.0448 1.347,0.0448 1.347,0.0448c0,0 0.539,-0.4036 0.539,-0.4036c0,0 1.29401,0.7624 1.29401,0.7624c0,0 1.94,0.1346 1.994,0.1346c0.05399,0 0.64699,0.8073 0.64699,0.8073c0,0 0.59201,0.2242 0.59201,0.2242c0,0 0.91699,-0.5382 0.91699,-0.5382c0,0 -0.162,0.8522 -0.162,0.8522c0,0 0.97,-0.4934 0.97,-0.4934c0,0 -0.431,0.6279 -0.431,0.6279c0,0 0.754,0 0.754,0c0,0 0.32401,0.3588 0.37801,0.3588c0.054,0 0.916,0.4037 0.916,0.4037c0,0 0.647,-0.2691 0.647,-0.2691c0,0 0.754,0.6279 0.754,0.6279c0,0 1.401,0.0897 1.401,0.1345c0,0.0449 0.91699,0.3588 1.024,0.3588c0.108,0 0.91699,-0.7176 0.91699,-0.7176c0,0 0,0.4485 0,0.4934c0,0.0448 0.97,-1.0316 0.97,-1.0316c0,0 0.269,0.7176 0.269,0.7176c0,0 -0.808,0.8522 -0.808,0.897c0,0.0449 -0.054,0.5831 -0.054,0.5831c0,0 0.108,0.7176 0.054,0.7176c-0.054,0 0.431,0.6727 0.431,0.6727c0,0 -0.431,0.583 -0.431,0.583c0,0 0.054,1.0765 0.054,1.0765c0,0 0.539,0.6278 0.539,0.6278c0,0 0.539,0.4037 0.539,0.3588c0,-0.0448 0.862,0.4037 0.862,0.4037c0,0 -0.647,0.2691 -0.647,0.2691c0,0 -0.161,0.6279 -0.161,0.6279c0,0 -0.80901,-0.9867 -0.80901,-0.9867c0,0 -0.754,0.3588 -0.754,0.3588c0,0 0.323,0.4485 0.323,0.4485c0,0 -1.078,0.3588 -1.078,0.3588c0,0 -0.323,-0.5831 -0.323,-0.5831c0,0 -0.26999,0.7625 -0.26999,0.7625c0,0 -0.485,0.1794 -0.485,0.1794c0,0 -0.431,-0.4485 -0.431,-0.4485c0,0 -0.323,0.6279 -0.323,0.6279c0,0 0.215,0.6279 0.215,0.6279c0,0 -1.455,-0.4037 -1.401,-0.4037c0.054,0 -0.80901,0.3588 -0.80901,0.4037c0,0.0448 -0.161,0.6279 -0.161,0.6279c0,0 -1.078,0.3588 -1.078,0.4036c0,0.0449 -0.647,1.0316 -0.647,1.0316c0,0 -0.269,1.0764 -0.269,1.0764c0,0 -0.755,1.2109 -0.755,1.2109c0,0 -0.054,0.7625 -0.054,0.7625c0,0 -0.64699,0.2242 -0.64699,0.2242c0,0 -0.431,-0.4036 -0.431,-0.4036c0,0 -0.70001,0.583 -0.70001,0.583c0,0 0.26901,0.897 0.26901,0.897c0,0 -0.21501,0.6728 -0.21501,0.6728c0,0 -1.07799,0.269 -1.07799,0.269c0,0 -0.86301,0.08971 -0.86301,0.08971c0,0 -0.377,0.6728 -0.377,0.6728c0,0 -0.701,-0.0897 -0.701,-0.0897c0,0 -0.862,0.583 -0.862,0.6279c0,0.0448 -1.778,0.2691 -1.778,0.2691c0,0 -0.86299,-0.1346 -0.86299,-0.1346c0,0 -0.485,0.3588 -0.485,0.3588c0,0 -1.132,-0.6279 -1.132,-0.6279c0,0 -1.024,0.0897 -1.024,0.0897c0,0 -1.239,1.0764 -1.239,1.0764c0,0 -1.40201,-0.583 -1.40201,-0.583c0,0 -0.808,0.3139 -0.808,0.3139c0,0 -1.07799,0.5382 -1.07799,0.5382c0,0 -1.132,0.1346 -1.132,0.1346c0,0 -0.646,0.583 -0.646,0.583c0,0 -0.755,-0.2691 -0.755,-0.2691c0,0 -0.162,-0.6279 -0.162,-0.6279c0,0 -1.185,-0.1345 -1.185,-0.1345c0,0 -0.647,-0.4037 -0.647,-0.4037c0,0 -0.755,0.4485 -0.755,0.4934c0,0.0448 -0.59299,0.2242 -0.59299,0.2242c0,0 -0.97,-0.1345 -0.97,-0.1345c0,0 -0.377,0.4036 -0.377,0.4036c0,0 -0.701,-0.2691 -0.701,-0.2691c0,0 -0.592,0.2691 -0.592,0.2691c0,0 -0.054,-0.8073 -0.054,-0.8073c0,0 -0.809,-0.0897 -0.809,-0.0897c0,0 -0.485,-0.6279 -0.539,-0.6279c-0.054,0 -0.97,0.1346 -0.97,0.1346c0,0 -1.078,0.3139 -1.078,0.3139c0,0 -0.485,0.6728 -0.485,0.6728c0,0 -0.485,0.3588 -0.485,0.3588c0,0 -1.078,-0.4485 -1.078,-0.4485c0,0 -1.132,-0.17941 -1.132,-0.17941c0,0 -0.916,-0.897 -0.916,-0.897c0,0 -0.75401,-0.4037 -0.808,-0.4037c-0.054,0 -1.24001,0.0897 -1.24001,0.0897c0,0 -1.617,0.1346 -1.617,0.1346c0,0 -0.97,0.583 -0.97,0.583c0,0 -1.509,0.17941 -1.509,0.2243c0,0.0448 -0.862,-0.1794 -0.862,-0.1794c0,0 -0.916,0.2242 -0.916,0.2242c0,0 -0.647,-0.7624 -0.647,-0.7624c0,0 -1.294,0.2242 -1.294,0.2242c0,0 -0.862,0.4934 -0.862,0.4934c0,0 -1.886,-0.5831 -1.886,-0.5831c0,0 -0.917,-0.4485 -0.97,-0.4485c-0.054,0 -1.24,0.0449 -1.24,0.0449c0,0 -0.755,-0.8073 -0.80801,-0.8073c-0.05399,0 -1.725,0.98669 -1.725,1.0315c0,0.0449 0.916,0.0449 0.916,0.0449c0,0 -0.054,0.40369 -0.054,0.40369c0,0 -1.617,0.5381 -1.617,0.5381c0,0 -1.131,0.5831 -1.131,0.5382c0,-0.0448 -0.8089,-0.2691 -0.8089,-0.2691c0,0 -1.6708,0.0897 -1.6708,0.0897c0,0 -0.9162,-0.5382 -0.9162,-0.5382c0,0 -0.7006,-0.4933 -0.7006,-0.4933c0,0 -0.2156,0.7176 -0.2156,0.7176c0,0 1.0779,0.13451 1.0779,0.13451c0,0 -0.1078,0.4934 -0.1078,0.4934c0,0 -0.9701,0.1345 -0.9701,0.1345c0,0 0.7545,0.44849 0.7545,0.44849c0,0 -0.8623,0.2243 -0.8623,0.2243c0,0 -1.02399,-0.314 -1.02399,-0.314c0,0 -1.4552,-0.9418 -1.4552,-0.9418c0,0 -0.2156,-0.76241 -0.2156,-0.76241c0,0 -0.7545,0.0448 -0.7545,0.0448c0,0 -2.4253,-0.1346 -2.4253,-0.1346c0,0 -1.563,0.4934 -1.563,0.4934c0,0 -0.7006,0.6727 -0.7006,0.6727c0,0 -2.0481,-0.2242 -2.0481,-0.2242c0,0 -1.0779,-0.5382 -1.13181,-0.5382c-0.05389,0 -1.02399,0.1794 -1.02399,0.2242c0,0.0449 -0.43111,0.4485 -0.43111,0.4485c0,0 -0.80849,-0.0897 -0.80849,-0.0897c0,0 -0.1078,-1.7491 -0.1078,-1.7491c0,0 1.61691,-0.7625 1.61691,-0.7625c0,0 0.8084,0.2243 0.8084,0.2243c0,0 0.8085,-0.2691 0.8085,-0.2691c0,0 -1.9942,-0.897 -1.9942,-0.897c0,0 -0.8084,-0.1794 -0.8084,-0.1794c0,0 0.4851,-1.3455 0.4851,-1.3455c0,0 0.70061,-0.0897 0.70061,-0.0897c0,0 -0.70061,-0.5382 -0.70061,-0.5382c0,0 0,-0.583 0,-0.583c0,0 0.7545,-0.3588 0.7545,-0.3588c0,0 -0.1078,-1.3455 -0.1078,-1.3455c0,0 0.4851,-0.6279 0.4851,-0.6279c0,0 -0.4312,-0.5831 -0.4312,-0.5831c0,0 1.4552,-1.3006 1.4552,-1.3006c0,0 -0.16171,-0.8073 -0.16171,-0.8073c0,0 -0.7006,-0.5382 -0.7006,-0.5382c0,0 -0.2156,-0.897 -0.2156,-0.897c0,0 -0.5929,-0.7624 -0.5929,-0.7624c0,0 -0.2156,-0.7625 -0.2156,-0.7625c0,0 -1.02399,-0.4485 -1.02399,-0.4485c0,0 -0.8623,-0.0448 -0.8623,-0.0448c0,0 -1.024,-1.3904 -1.024,-1.3904c0,0 -0.7007,-1.4352 -0.7007,-1.4352l-0.00001,0.00002zm52.6017,17.8502c-0.054,0 -1.509,-0.13451 -1.509,-0.13451c0,0 0.32401,0.4036 0.32401,0.4036l1.185,-0.2691l-0.00001,0.00001zm-17.138,2.4668c0,0 0.75401,-0.7176 0.75401,-0.7176c0,0 0.701,-0.0449 0.701,-0.0449c0,0 -0.324,0.3588 -0.324,0.3588c0,0 -1.131,0.4037 -1.131,0.4037l-0.00001,0zm-27.32539,-1.6595c0,0 -1.3474,0.0897 -1.3474,0.1346c0,0.0448 0.7006,0.22421 0.7006,0.22421c0,0 0.6468,-0.3588 0.6468,-0.3588l0,-0.00001zm-4.31171,-1.0315c0,0 -0.5928,0.4485 -0.5928,0.4485c0,0 0.4311,0.1794 0.4311,0.2242c0,0.0449 0.4312,-0.2242 0.4312,-0.2242c0,0 -0.2695,-0.4485 -0.2695,-0.4485z";
                    }
                    return (
                        <Geography
                            key={geo.rsmKey}
                            stroke="#FFF"
                            geography={geo}
                            fill={fillcolor}
                            onClick={this.handleClick}
                            data-id={cur.id}
                        />
                    )
                  })}
                  {geographies.map(geo => {
                    const centroid = geoCentroid(geo);
                    const cur = allStates.find(s => s.val === geo.id);
                    var fillclass='';
                    if (cur.id === 'HI') {
                      fillclass = 'transparenttext';
                    }
                    if (cur.id === 'GU') {
                      fillclass = 'transparenttext';
                    }
                    return (
                        <g key={geo.rsmKey + "-name"}>
                          {cur &&
                          cur.id !== 'GU' &&
                          centroid[0] > -160 &&
                          centroid[0] < -67 &&
                          (Object.keys(offsets).indexOf(cur.id) === -1 ? (
                              <Marker coordinates={centroid}>
                                <text y="2" fontSize={14} textAnchor="middle" className={fillclass}>
                                  {cur.id}
                                </text>
                              </Marker>
                          ) : (
                              <Annotation
                                  subject={centroid}
                                  dx={offsets[cur.id][0]}
                                  dy={offsets[cur.id][1]}
                              >
                                <text x={4} fontSize={14} alignmentBaseline="middle">
                                  {cur.id}
                                </text>
                              </Annotation>
                          ))}
                          {cur &&
                          cur.id === 'GU' &&
                            <g transform="translate(315.179191, 520.5772887463959)" class="rsm-marker ">
                                <text y="2" fontSize={14} textAnchor="middle" className={fillclass}>
                                  {cur.id}
                                </text>
                            </g>
                          }
                          {cur &&
                          cur.id === 'PR' &&
                            <g transform="translate(740.179191, 560.5772887463959)" class="rsm-marker ">
                                <text y="2" fontSize={14} textAnchor="middle" className={fillclass}>
                                  {cur.id}
                                </text>
                            </g>
                          }
                          </g>
                    );
                  })}
                </>
            )}
          </Geographies>
        </ComposableMap>
          <div className="container flex-justify">
            <div className="item2"><h5>Grantees </h5></div>
            <div className="item2">
              <div className="county_d">
                <div class="label">Search by County or Independent City</div>
                <Select
                    value={this.state.county}
                    onChange={this.handleCountyChange}
                    options={countyitems}
                />
              </div>
            </div>
            <div className="item2">
              <div className="zip_d">
                <div className="label">Search by Zipcode</div>
                <Select
                    value={this.state.zip}
                    onChange={this.handleZipChange}
                    options={zipitems}
                />
              </div>
            </div>
          </div>
          <div className="container">
            <div className="item">Details</div>
          </div>
          {items}
          {items.length === 0 &&
          <div className="message">If the county you are looking for is not listed, please visit the <a
              href="https://www.careeronestop.org/LocalHelp/service-locator.aspx">Service Locator Local Help |
            CareerOneStop</a> website to find a local American Job Center that can assist you</div>
          }
        </div>
    );
  };
};

export default MapChart;
