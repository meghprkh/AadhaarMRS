var React = require('react');
var Router = require('react-router');
var { Route, DefaultRoute, RouteHandler, Link, Navigation } = Router;

// Components
var NavBar =require('./navbar.js');
//var Login=require('./login.js');

var Home=require('./home.js');

var Login=require('./login.js');
var Login_Patient=require('./login_patient.js');
var Login_Doctor=require('./login_doctor.js');

var Register=require('./register.js');
var Register_Patient=require('./register_patient.js');
var Register_Doctor=require('./register_doctor.js');

var Logout=require('./logout.js');

var Doctor_EnterPatient = require('./doctor/enterpatient.js');
var Doctor_PatientPanel_tabbar = require('./doctor/patientpanel_tabbar.js');
var Doctor_PastRecords = require('./doctor/pastrecords.js');
var Doctor_Vaccines = require('./doctor/vaccines.js');
var Doctor_Allergies = require('./doctor/allergies.js');
var Doctor_Vitals = require('./doctor/vitals.js');
var Doctor_Vitals_edit = require('./doctor/vitals_edit.js');

//var Patient= require('./patient_handler.js');
var Patient_PatientPanel_tabbar = require('./patient/patientpanel_tabbar.js');
var Patient_PastRecords = require('./patient/pastrecords.js');
var Patient_Vaccines = require('./patient/vaccines.js');
var Patient_Allergies = require('./patient/allergies.js');
var Patient_Vitals = require('./patient/vitals.js');

var App = React.createClass({
  render: function () {
    return (
      <div>
        <NavBar />
        <RouteHandler/>
      </div>
    );
  }
});

var routes = (
  <Route name="app" path="/" handler={App}>
    <Route name="doctor" path="doctor">
      <Route name="doctor_patient" path="patient/:pid/" handler={Doctor_PatientPanel_tabbar}>
        <Route name="doctor_past_records" path="past_records" handler={Doctor_PastRecords} />
        <Route name="doctor_vaccines" path="vaccines" handler={Doctor_Vaccines} />
        <Route name="doctor_allergies" path="allergies" handler={Doctor_Allergies} />
        <Route name="doctor_vitals" path="vitals" >
          <Route name="doctor_vitals_edit" path="edit" handler={Doctor_Vitals_edit} />
          <DefaultRoute handler={Doctor_Vitals} />
        </Route>
        <DefaultRoute handler={Doctor_PastRecords} />
      </Route>
      <DefaultRoute handler={Doctor_EnterPatient} />
    </Route>
    <Route name="patient" handler={Patient_PatientPanel_tabbar} path="patient">
      <Route name="patient_past_records" path="past_records" handler={Patient_PastRecords} />
      <Route name="patient_vaccines" path="vaccines" handler={Patient_Vaccines} />
      <Route name="patient_allergies" path="allergies" handler={Patient_Allergies} />
      <Route name="patient_vitals" path="vitals" handler={Patient_Vitals} />
      <DefaultRoute handler={Patient_PastRecords} />
    </Route>
    <Route name="login" handler={Login} path="login" >
      <Route path="doctor" name="login_doctor" handler={Login_Doctor} />
      <Route path="patient" name="login_patient" handler={Login_Patient} />
      <DefaultRoute handler={Login_Patient}/>
    </Route>
    <Route name="register" handler={Register} path="register" >
      <Route path="doctor" name="register_doctor" handler={Register_Doctor} />
      <Route path="patient" name="register_patient" handler={Register_Patient} />
      <DefaultRoute handler={Login_Patient}/>
    </Route>
    <Route name="logout" handler={Logout} path="logout" />
    <DefaultRoute handler={Home}/>
  </Route>
);

Router.run(routes, function (Handler) {
  React.render(<Handler/>, document.body);
});
