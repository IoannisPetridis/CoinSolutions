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

import android.content.Intent;
import android.content.SharedPreferences;
import android.util.Log;

import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.FirebaseInstanceIdService;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import salamantex.coinsolutions.MainActivity;
import salamantex.coinsolutions.Network.TalkToServer;

import static salamantex.coinsolutions.MainActivity.devEmail;
import static salamantex.coinsolutions.MainActivity.server;


public class MyFirebaseInstanceIDService extends FirebaseInstanceIdService {

    private static final String TAG = "MyFirebaseIIDService";
    SharedPreferences preferences;
    SharedPreferences.Editor editor;

    /**
     * Called if InstanceID token is updated. This may occur if the security of
     * the previous token had been compromised. Note that this is called when the InstanceID token
     * is initially generated so this is where you would retrieve the token.
     */
    // [START refresh_token]
    @Override
    public void onTokenRefresh() {
        // Get updated InstanceID token.
        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
        preferences = getSharedPreferences("Preferences", MODE_PRIVATE);
        editor = preferences.edit();
        editor.putString("token",refreshedToken);
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
    }
    // [END refresh_token]

    /**
     * Persist token to third-party servers.
     *
     * Modify this method to associate the user's FCM InstanceID token with any server-side account
     * maintained by your application.
     *
     * @param token The new token.
     */
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
}
