/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package salamantex.coinsolutions.notifications;

import android.app.NotificationManager;
import android.app.PendingIntent;

import android.content.Context;
import android.content.Intent;

import android.content.SharedPreferences;
import android.graphics.BitmapFactory;
import android.media.RingtoneManager;
import android.net.Uri;

import android.support.v4.app.NotificationCompat;
import android.support.v4.content.LocalBroadcastManager;
import android.util.Log;

import com.firebase.jobdispatcher.FirebaseJobDispatcher;
import com.firebase.jobdispatcher.GooglePlayDriver;
import com.firebase.jobdispatcher.Job;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;


import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.Map;


import salamantex.coinsolutions.Network.TalkToServer;
import salamantex.coinsolutions.ProfilePage;
import salamantex.coinsolutions.R;


import static salamantex.coinsolutions.MainActivity.activeClass;
import static salamantex.coinsolutions.MainActivity.devEmail;
import static salamantex.coinsolutions.MainActivity.server;

public class MyFirebaseMessagingService extends FirebaseMessagingService {

    private static final String TAG = "MyFirebaseMsgService";
    SharedPreferences preferences;
    SharedPreferences.Editor editor;
    Callbacks activity;


    private LocalBroadcastManager broadcaster;


    @Override
    public void onCreate() {
        broadcaster = LocalBroadcastManager.getInstance(this);
    }

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        NotificationManager nMgr = (NotificationManager) getApplicationContext().getSystemService(Context.NOTIFICATION_SERVICE);
        nMgr.cancelAll();
        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
        preferences = getSharedPreferences("Preferences", MODE_PRIVATE);
        editor = preferences.edit();
        editor.putString("token", refreshedToken);
        editor.apply(); //Instead of commit, this is handled in the background
        Log.e(TAG, "Refreshed token: " + refreshedToken);

        // If you want to send messages to this application instance or
        // manage this apps subscriptions on the server side, send the
        // Instance ID token to your app server.
        try {
            sendRegistrationToServer(refreshedToken);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        // Not getting messages here? See why this may be: https://goo.gl/39bRNJ
        Log.e(TAG, "From: " + remoteMessage.getFrom());


        // Check if message contains a data payload.
        if (remoteMessage.getData().size() > 0) {
            Log.e(TAG, "Message data payload: " + remoteMessage.getData());

            if (/* Check if data needs to be processed by long running job */ true) {
                // For long-running tasks (10 seconds or more) use Firebase Job Dispatcher.
                scheduleJob();
            } else {
                // Handle message within 10 seconds
                handleNow();
            }
        }

        // Check if message contains a notification payload.
        if (remoteMessage.getNotification() != null) {
            Log.e(TAG, "Message Notification Body: " + remoteMessage.getNotification().getBody());
            sendNotification(remoteMessage);
        }
        // Also if you intend on generating your own notifications as a result of a received FCM
        // message, here is where that should be initiated. See sendNotification method below.
    }
    // [END receive_message]

    private void sendRegistrationToServer(String token) throws JSONException {
        //Implement this method to send token to your app server.
        String url = server + "syncToken.php?pass=masterpass";
        JSONArray ar = new JSONArray();
        JSONObject obj = new JSONObject();
        obj.put("email", devEmail);
        obj.put("token", token);
        ar.put(obj);

        String data = ar.toString();

        //TODO: Changed here - SHOULD WORK FINE
        Intent msgIntent = new Intent(this, TalkToServer.class);
        //Here we define the parameters (url, data)
        //basically the target php script and the data that's going to be send to it
        msgIntent.putExtra("url", url);
        msgIntent.putExtra("data", data);
        startService(msgIntent);
    }

    /* Schedule a job using FirebaseJobDispatcher. */
    private void scheduleJob() {
        // [START dispatch_job]
        FirebaseJobDispatcher dispatcher = new FirebaseJobDispatcher(new GooglePlayDriver(this));
        Job myJob = dispatcher.newJobBuilder()
                .setService(MyJobService.class)
                .setTag("my-job-tag")
                .build();
        dispatcher.schedule(myJob);
        // [END dispatch_job]
    }

    /**
     * Handle time allotted to BroadcastReceivers.
     */
    private void handleNow() {
        Log.d(TAG, "Short lived task is done.");
    }

    /**
     * Create and show a simple notification containing the received FCM message.
     *
     * @param remoteMessage FCM remote message received.
     */
    private void sendNotification(RemoteMessage remoteMessage) {
        Map<String, String> data = remoteMessage.getData();
        String messageBody = remoteMessage.getNotification().getBody();
        String title = remoteMessage.getNotification().getTitle();

        Uri defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
        NotificationCompat.Builder notificationBuilder = null;

        if (data.containsKey("notification_type")) {
            if (data.get("notification_type").equals("message")) {
                //Message
                Log.e("TAG","ISSUING NOTIFICATION!");

                Intent intent = new Intent("messageBroadcast");
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                if (data.containsKey("source")) {
                    intent.putExtra("source", data.get("source"));
                }
                if (data.containsKey("target")) {
                    intent.putExtra("target", data.get("target"));
                }
                if (data.containsKey("amount")) {
                    intent.putExtra("amount", data.get("amount"));
                }
                if (data.containsKey("cur_type")) {
                    intent.putExtra("cur_type", data.get("cur_type"));
                }
                if (data.containsKey("processed")) {
                    intent.putExtra("processed", data.get("processed"));
                }
                if (data.containsKey("message")) {
                    intent.putExtra("message", data.get("message"));
                }

                Intent broadcast = new Intent(getApplicationContext(), activeClass);
                broadcast.setAction("messageBroadcast");
                broadcast.putExtra("message", messageBody);
                //sendBroadcast(broadcast);
                broadcaster.sendBroadcast(broadcast);

                //PendingIntent pendingIntent = PendingIntent.getActivity(this, 0 /* Request code */, intent, PendingIntent.FLAG_ONE_SHOT);
                /*notificationBuilder = new NotificationCompat.Builder(this)
                        .setTicker(messageBody)
                        .setSmallIcon(R.mipmap.ic_launcher)
                        .setLargeIcon(BitmapFactory.decodeResource(this.getResources(), R.mipmap.ic_launcher))
                        .setContentTitle(title)
                        .setContentText(messageBody)
                        .setAutoCancel(true)
                        .setSound(defaultSoundUri)
                        .setContentIntent(pendingIntent);

                NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);*/
                //notificationManager.notify(0 /* ID of notification */, notificationBuilder.build());
                }
            }
        }
    }