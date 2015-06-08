var React = require('react');
var Router = require('react-router');
var { Route, DefaultRoute, RouteHandler, Link, Navigation } = Router;

var EnterPatient = React.createClass({
  mixins: [Navigation],
  contextTypes: {
    router: React.PropTypes.func
  },
  submitHandler:function() {
    this.transitionTo("doctor_patient",
                  {pid:document.getElementById('aadhaarNumber').value});
  },
  render: function() {
    return (
      <div className="container">
        <form className="smallForm" onSubmit={this.submitHandler}>
          <div className="form-group">
            <label>Enter Patient's Aadhaar Number</label>
            <input placeholder="Aadhaar Number" className="form-control"
                id="aadhaarNumber"/>
          </div>
          <button type="submit" className="btn btn-primary">Search</button>
        </form>
      </div>
    );
  }
});

module.exports=EnterPatient
