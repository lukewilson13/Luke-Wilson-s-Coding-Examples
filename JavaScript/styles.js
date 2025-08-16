import { StyleSheet, Platform } from "react-native";

export default StyleSheet.create({
  // app.js
  body: {
    height: 1000,
    backgroundColor: '#717171',
  },
  nav: {
    backgroundColor: 'blue',
    alignItems: 'center',
},


  // alarm.js
  container: {
    width: '100%',
    height: '100%',
    flexDirection: 'column',
    justifyContent: 'flex-start',
    alignItems: 'center',
    backgroundColor: 'black',
    paddingTop: 40,
  },

  flatlistsView: {
    flexDirection: 'row',
    height: 150,
    marginLeft: Platform.OS === 'ios' ? 70 : 0,
  },

  flatlist: {
    flexDirection: 'column',
    marginBottom: 10,
    paddingHorizontal: Platform.OS === 'web' ? 25 : 0,
  },

  listText: {
    color: 'ghostwhite',
    fontSize: 40,
  },

  createAlarm: {
    backgroundColor: '#98FB98',
    borderRadius: 10,
    height: 70,
    width: '50%',
    marginVertical: 30,
    padding: 10,
    justifyContent: 'center',
    alignItems: 'center',
  },

  createText: {
    fontSize: 20,
  },

  invalid: {
    color: '#FF4F4B',
    marginTop: 10,
  },

  activeAlarms: {
    flexDirection: 'row',
    alignItems: 'center',
  },

  deletePressable: {
    justifyContent: 'center',
    height: 50,
    backgroundColor: '#ff9194',
    borderWidth: 1,
    borderColor: 'ghostwhite', 
    borderRadius: 10, 
    paddingHorizontal: 5,
  },

  deleteText: {
    color: 'ghostwhite',
    fontSize: 18,
  },

  displayText: {
    color: 'ghostwhite',
    fontSize: 60,
    width: 270,
    margin: 5,
    borderWidth: 1,
    borderColor: 'ghostwhite', 
    borderRadius: 10, 
    padding: 10, 
  },

  finished: {
    fontSize: 40,
    color: 'white',
    marginTop: 75,
  },

  finishedSmall: {
    fontSize: 20,
    color: 'white',
    marginTop: 20,
  },

  done: {
    backgroundColor: 'lightgrey',
    width: 200,
    textAlign: 'center',
    marginTop: 150,
    marginBottom: 50,
    borderRadius: 10,
  },

  doneText: {
    color: 'black',
    fontSize: 24,
    padding: 20,
    textAlign: 'center',
  },

  snooze: {
    backgroundColor: '#98FB98',
    width: 200,
    textAlign: 'center',
    width: 150,
    marginBottom: 50,
    borderRadius: 10,
  },

  snoozeText: {
    color: 'black',
    fontSize: 24,
    padding: 20,
    textAlign: 'center',
  },
});