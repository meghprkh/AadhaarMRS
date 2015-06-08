var React = require('react');
var Router = require('react-router');
var { Route, DefaultRoute, RouteHandler, Link, Navigation } = Router;


var Home=React.createClass({
  mixins: [Navigation],
  contextTypes: {
    router: React.PropTypes.func
  },
  componentDidMount: function() {
    if(session.uid) {
      if(session.doctor) this.transitionTo("doctor");
      else this.transitionTo("patient");
    }
  },
  render: function() {
    return (
      <div className="container">
        <div className="row">
          <div className="col-md-6 col-xs-12">
            <div className="jumbotron">
              <h1>Users</h1>
              <div className="btn-toolbar" role="group" aria-label="...">
                <Link to="/login/patient" className="btn btn-primary btn-lg">Login</Link>
                <Link to="/register/patient" className="btn btn-primary btn-lg">Register</Link>
              </div>
            </div>
          </div>
          <div className="col-md-6 col-xs-12">
            <div className="jumbotron">
              <h1>Doctors</h1>
              <div className="btn-toolbar" role="group" aria-label="...">
                <Link to="/login/doctor" className="btn btn-primary btn-lg">Login</Link>
                <Link to="/register/doctor" className="btn btn-primary btn-lg">Register</Link>
              </div>
            </div>
          </div>
        </div>
      </div>);
  }
});

module.exports=Home;
