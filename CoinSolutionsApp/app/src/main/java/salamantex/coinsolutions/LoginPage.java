package salamantex.coinsolutions;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.Color;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.support.v4.content.LocalBroadcastManager;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.EditText;

import com.ontbee.legacyforks.cn.pedant.SweetAlert.SweetAlertDialog;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.Objects;

import salamantex.coinsolutions.Network.TalkToServer;

import static salamantex.coinsolutions.MainActivity.activeClass;
import static salamantex.coinsolutions.MainActivity.devEmail;
import static salamantex.coinsolutions.MainActivity.server;

/**
 * Created by EEUser on 13/02/2018.
 */

public class LoginPage extends AppCompatActivity {

    Context activeContext;
    SweetAlertDialog pDialog;
    String response;
    EditText email;
    String email_text;

    IntentFilter filter;
    BroadcastReceiver receiver;
    boolean receiverReg = false;
    CountDownTimer countdown;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login_page);
        activeContext = this;
        activeClass = this.getClass();
        email = (EditText) findViewById(R.id.email_text);
        setReceiver();
    }

    @Override
    protected void onStart() {
        super.onStart();
        if (!receiverReg) {
            setReceiver();
        }
    }

    @Override
    protected void onResume() {
        /*This is the state in which the app interacts with the user*/
        super.onResume();
    }

    @Override
    protected void onPause() {
        super.onPause();
    }

    @Override
    protected void onStop() {
        super.onStop();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (receiverReg) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(receiver);
        }
    }

    @Override
    public void onBackPressed() {
        Intent intent = new Intent(activeContext, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        startActivity(intent);
        finish();
    }

    public void setReceiver() {
        filter = new IntentFilter();
        filter.addAction("serverResponseLogin");
        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context activeContext, Intent intent) {
                if (intent.getAction().equals("serverResponseLogin")) {
                    response = intent.getStringExtra("result");
                }
            }
        };
        if (!receiverReg) {
            LocalBroadcastManager.getInstance(this).registerReceiver(receiver, filter);
            receiverReg = true;
        }
    }

    public void onLoginClick(View v) {
        email_text = email.getText().toString();
        if (email_text.equals("")) {
            //If email is empty
            pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                    .setTitleText("Empty email")
                    .setContentText("Email field must not be empty!")
                    .setConfirmText("Got it")
                    .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                        @Override
                        public void onClick(SweetAlertDialog sDialog) {
                            pDialog.dismissWithAnimation();
                            email.setHintTextColor(Color.RED);
                            email.setText("");
                        }
                    });
            pDialog.show();
        }
        else {
            String data = prepareData();
            String url = server + "login.php?pass=masterpass";

            //TODO: Changed here
            Intent msgIntent = new Intent(activeContext, TalkToServer.class);
            //Here we define the parameters (url, data)
            //basically the target php script and the data that's going to be send to it
            msgIntent.putExtra("url", url);
            msgIntent.putExtra("data", data);
            startService(msgIntent);

            pDialog = new SweetAlertDialog(this, SweetAlertDialog.PROGRESS_TYPE);
            pDialog.getProgressHelper().setBarColor(Color.parseColor("#A5DC86"));
            pDialog.setTitleText("Logging you in, please wait...");
            pDialog.setCancelable(false);
            pDialog.show();
            countdown = new CountDownTimer(3000, 1000) {
                @Override
                public void onTick(long l) {

                }

                @Override
                public void onFinish() {
                    pDialog.dismissWithAnimation();
                    if (response != null) {
                        if (Objects.equals(response, "unknown user")) {
                            pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                                    .setTitleText("Unrecognised email")
                                    .setContentText("We cannot recognise the email: '" + email_text +"!")
                                    .setConfirmText("Got it")
                                    .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                                        @Override
                                        public void onClick(SweetAlertDialog sDialog) {
                                            pDialog.dismissWithAnimation();
                                            email.setHintTextColor(Color.RED);
                                            email.setText("");
                                        }
                                    });
                            pDialog.show();
                        } else if (Objects.equals(response, "OK")) {
                            devEmail = email_text;
                            Intent intent = new Intent(activeContext, ProfilePage.class);
                            intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
                            //Here maybe send data to that activity with the command below
                            //intent.putExtra(EXTRA_MESSAGE, message);
                            startActivity(intent);
                        }
                    }
                }
            }.start();
        }
    }

    public String prepareData() {
        JSONArray ar = new JSONArray();
        JSONObject obj = new JSONObject();
        try {
            obj.put("email",email_text);
            ar.put(obj);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return ar.toString();
    }

}