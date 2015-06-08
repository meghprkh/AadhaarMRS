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
      otp_generated:false,
      failure:false
    }
  },
  verify: function() {
    qwest.post('register/user/',{
            "aadhaar-id":document.getElementById("login-aadhaar-id").value,
            "otp":document.getElementById("login-otp").value})
          .then(function(response) {
            console.log(response);
            if(response.success) {
              session.uid=response.uid;
              session.token=response.token;
              session.name=response.name;
              session.doctor=false;
              this.transitionTo("patient");
            } else {
              this.setState({failure:true,otp_generated:false})
            }
          }.bind(this));
  },
  otpGen:function(e) {
    qwest.post('otp/',{"aadhaar-id":document.getElementById("login-aadhaar-id").value})
          .then(function(response) {
            console.log(response);
            if(response.success)
              this.setState({otp_generated:true});
          }.bind(this));
  },
  render:function(){
    return (
      <div>
        {this.state.failure?
          (<div className="alert alert-danger" role="alert">Invalid Aadhaar number/OTP</div>)
          :""}
        <div className="form-group">
          <label>Your Aadhaar Number</label>
          <input id="login-aadhaar-id" placeholder="Aadhaar Number"
              className="form-control"
              defaultValue="223334065242"/>
        </div>

        {this.state.otp_generated?
          <div>
            <div className="form-group">
              <label>OTP</label>
              <input id="login-otp" placeholder="OTP" className="form-control"/>
            </div>
            <button className="btn btn-primary" onClick={this.verify}>Login</button>
          </div>:
          <button className="btn btn-primary" onClick={this.otpGen}>Generate OTP</button>
        }
      </div>
    )
  }
});

module.exports=Form;
