var React = require("react");
var Router = require('react-router');
var { Route, DefaultRoute, RouteHandler, Link, Navigation } = Router;

var TabBar = require("./tabbar.js");

var qwest=require('qwest');
qwest.base = 'https://aadhaarmrs-meghprkh.rhcloud.com/api/';



var Form = React.createClass({
  mixins: [Navigation],
  contextTypes: {
    router: React.PropTypes.func
  },
  getInitialState: function(){
    return {
      tab:"register_patient"
    }
  },
  tabHandler: function(id) {
    this.setState({tab:id});
    this.transitionTo(id);
  },
  render:function(){
    return (
      <div className="smallForm container">
        <TabBar tabs={[{"id":"register_patient","value":"User"},{"id":"register_doctor","value":"Doctor"}]}
          active={this.state.tab}
          handler={this.tabHandler}/>
          <br />
        <RouteHandler />
      </div>
    )
  }
})

module.exports=Form;
