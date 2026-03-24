import { useState, useEffect } from 'react';
import { View, FlatList, Text, Pressable, ScrollView } from 'react-native';
import styles from "./styles";

// CREATES HOURS FOR FLATLIST
const hours = new Array(11).fill(null).map((v, i) => ({key: (i + 1).toString(), hour: i + 1}));
hours.unshift({key: '12', hour: 12}) // https://www.w3schools.com/jsref/jsref_unshift.asp

// CREATES MINUTES FOR FLATLIST
const baseMinutes = new Array(60).fill(null).map((v, i) => ({key: i.toString(), minute: i}));
const minutes = baseMinutes.map((item) => { // changes format of minute to 00, 01, 02, etc.
  return { key: item.key.padStart(2, '0'), minute: item.minute }; // https://www.w3schools.com/jsref/jsref_string_padstart.asp
});

// Creates AM/PM FOR FLATLIST
const dayNightOptions = new Array({key: 'AM', value: 'AM'}, {key: 'PM', value: 'PM'});

export default function Alarm() {
  const [hour, setHour] = useState(12);
  const [minute, setMinute] = useState(0);
  const [dayNight, setDayNight] = useState('AM');
  const [displayMinute, setDisplayMinute] = useState('00');
  const [alarms, setAlarms] = useState([]);
  const [alarmsSeconds, setAlarmsSeconds] = useState([]);
  const [alarmFinished, setAlarmFinished] = useState(false);
  const [invalidAlarm, setInvalidAlarm] = useState(false);

// ON PRESS METHODS
  const onPressHour = (hour) => {
    setHour(hour);
  }
  const onPressMinute = (item) => {
    setDisplayMinute(item.key)
    setMinute(item.minute);
  }
  const onPressDayNight = (value) => {
    setDayNight(value);
  }

  // LOGIC FOR ALARM CREATION.
  const createAlarm = () => {
    // handles 12 AM/PM properly
    let adjustedHour = hour;
    if (hour === 12 && dayNight === 'AM') {
      adjustedHour = 0;
    } else if (dayNight === 'PM' && hour !== 12) {
      adjustedHour += 12;
    }

    // calculates alarm in seconds
    const currentDate = new Date();
    const alarmDate = new Date (currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), adjustedHour, minute, 0, 0)
    // https://stackoverflow.com/questions/4754938/ need getFullYear() instead of getYear()
    const alarmTime = alarmDate.getTime();
    const currentTime = currentDate.getTime(); // https://www.toptal.com/software/definitive-guide-to-datetime-manipulation
    const secondsRemaining = (alarmTime - currentTime) / 1000; // converting milliseconds to seconds

    // sets alarm
    if (secondsRemaining > 0) { // if alarm is valid...
      setInvalidAlarm(false);
      setAlarms((prevAlarms) => [...prevAlarms, {hour, displayMinute, dayNight}]);
      setAlarmsSeconds((prevAlarmsSeconds) => [...prevAlarmsSeconds, secondsRemaining]);
    } else {
      setInvalidAlarm(true); // displays invalid alarm message
    }
  }

  // LOGIC FOR DELETING AN ALARM
  const deleteAlarm = (index) => {
    const updatedAlarms = [...alarms];
    updatedAlarms.splice(index, 1);
    setAlarms(updatedAlarms);

    const updatedAlarmSeconds = [...alarmsSeconds];
    updatedAlarmSeconds.splice(index, 1);
    setAlarmsSeconds(updatedAlarmSeconds);
  }

  useEffect(() => {
    console.log("secs remaining: ", alarmsSeconds);
  }, [alarmsSeconds]);

  // useEffect(() => {
  //   console.log("alarms: ", alarms);
  // }, [alarms]);
  
  // LOGIC FOR ALARM PROCESSING
  useEffect(() => { // https://www.geeksforgeeks.org/how-to-call-a-function-repeatedly-every-5-seconds-in-javascript/
    const interval = setInterval(() => {
      setAlarmsSeconds(prevAlarmsSeconds => {
        return prevAlarmsSeconds.map(seconds => seconds - 1);
      });
    }, 1000);

    return () => clearInterval(interval);
  }, []);

  // CHECKS IF ALARM IS OVER
  useEffect(() => {
    alarmsSeconds.map((i, index) => {
      if (i < 0) {
        setAlarmFinished(true);
        deleteAlarm(index);
        // start sound
      }
    });
  }, [alarmsSeconds]);

  // LOGIC FOR SNOOZING ALARM
  const snoozeAlarm = () => {
    // retrieves current time in order to add one minute for alarm
    let date = new Date();
    let snoozeHour = date.getHours();
    let snoozeMinute = (date.getMinutes() + 1).toString().padStart(2, "0");

    // makes sure minute can't exceed 59
    if (snoozeMinute > 59) { 
      snoozeHour += 1;
      snoozeMinute = 0;
    }
    // makes sure the hour can't exceed 12
    if (snoozeHour > 12) { 
      snoozeHour -= 12;
    }
    
    // Toggles AM/PM if needed
    if (snoozeHour === 12 && dayNight === "AM") {
      dayNight = "PM";
    } else if (snoozeHour === 12 && dayNight === "PM") {
      dayNight = "AM";
    }

    // sets alarm for one minute ahead of current time
    setAlarms((prevAlarms) => [...prevAlarms, { hour: snoozeHour, displayMinute: snoozeMinute, dayNight }]);
    setAlarmsSeconds((prevAlarmsSeconds) => [...prevAlarmsSeconds, 60]);
    setAlarmFinished(false);
    // stop sound

    // reset states
    setHour(12);
    setMinute(0);
    setDisplayMinute("00");
    setDayNight('AM');
  }

  return (
    alarmFinished ? (
    // IF ALARM IS RINGING...
    <View style={styles.container}>
      <Text style={styles.finished}>ALARM FINISHED</Text>
      <Text style={styles.finishedSmall}>Please select an option</Text>

    {/* TURNS OFF ALARM */}
    <Pressable style={styles.done}
      onPress={() => {
        // reset states
        setHour(12);
        setMinute(0);
        setDisplayMinute("00");
        setDayNight('AM');
        setAlarmFinished(false);
        // stop sound
      }}
    >
        <Text style={styles.doneText}>Turn off</Text>
    </Pressable>

    {/* SNOOZE BUTTON */}
    <Pressable style={styles.snooze}
      onPress={snoozeAlarm}>
        <Text style={styles.snoozeText}>Snooze</Text>
    </Pressable>
    </View>

    // OTHERWISE...
    ) : (
      <View style={styles.container}>
      {/* displays a flatlist for each of hours, minutes, and AM/PM  */}
      <View style={styles.flatlistsView}>

      {/* HOURS FLATLIST */}
      <FlatList 
        style={styles.flatlist}
        showsVerticalScrollIndicator={false}
        data={hours}
        renderItem={({ item }) => 
          <Pressable 
            style={styles.listPressable}
            onPress={() => onPressHour(item.hour)}>
              <Text style={[
                styles.listText, {color: hour === item.hour ? '#98FB98' : 'white'}
              ]}>
                {item.hour}
              </Text>
          </Pressable>}
        snapToInterval={53}
        decelerationRate={1}
        snapToAlignment='start'
      />
    
      {/* MINUTES FLATLIST */}
      <FlatList 
        style={styles.flatlist}
        showsVerticalScrollIndicator={false}
        data={minutes} 
        renderItem={({ item }) => 
          <Pressable 
            style={styles.listPressable}
            onPress={() => onPressMinute(item)}
            /* if the minute being rendered is the actively selected minute then the color will update */
          > 
            <Text style={[
                  styles.listText, {color: minute === item.minute ? '#98FB98' : 'white'}
                ]}>
                  {item.key}
            </Text>
          </Pressable>}
        snapToInterval={53} 
        decelerationRate={1}
        snapToAlignment='start' 
      />

      {/* AM/PM FLATLIST */}
      <FlatList
        style={styles.flatlist}
        showsVerticalScrollIndicator={false}
        data={dayNightOptions}
        renderItem={({ item }) => 
          <Pressable 
          style={styles.listPressable}
            onPress={() => onPressDayNight(item.value)}>
                <Text style={[
                  styles.listText, {color: dayNight === item.value ? '#98FB98' : 'white'}
                ]}>
                  {item.value}
                </Text>
          </Pressable>}
      />
      </View>

      {/* INVALID ALARM MESSAGE */}
      {invalidAlarm ? <Text style={styles.invalid}>Alarm is invalid</Text> : null}

      {/* CREATE ALARM BUTTON */}
      <Pressable style={styles.createAlarm} onPress={() => createAlarm(hour, minute, dayNight)}>
        <Text style={styles.createText}>Create Alarm</Text>
      </Pressable>
      
        {/* ACTIVE ALARMS */}
        <ScrollView style={styles.scrollView} showsVerticalScrollIndicator={false}>
          {alarms.map((alarm, i) => (
            <View style={styles.activeAlarms} key={i}>
              {/* Alarm text */}
                <Text style={styles.displayText}>
                  {alarm.hour}:{alarm.displayMinute}:{alarm.dayNight}
                </Text>

              {/* Delete Button */}
              <Pressable style={styles.deletePressable}
                onPress={() => {
                  deleteAlarm(i);
                }}
              >
                <Text style={styles.deleteText}>Delete</Text>
              </Pressable>
            </View>
            ))}
        </ScrollView>
    </View>
  ));
}