package salamantex.coinsolutions;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.Color;
import android.media.MediaPlayer;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Vibrator;
import android.support.v4.content.LocalBroadcastManager;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.Spinner;

import com.ontbee.legacyforks.cn.pedant.SweetAlert.SweetAlertDialog;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import salamantex.coinsolutions.Network.TalkToServer;

import static salamantex.coinsolutions.MainActivity.devEmail;
import static salamantex.coinsolutions.MainActivity.server;
import static salamantex.coinsolutions.MainActivity.activeClass;

/**
 * Created by EEUser on 13/02/2018.
 */

public class CreateTransaction extends AppCompatActivity {

    Context activeContext;
    Context theActiveContext;
    SweetAlertDialog pDialog;
    SweetAlertDialog activeDialog;
    String response;
    String spinnerMessage;
    Spinner spinner;

    EditText target;
    EditText amount;

    String target_text;
    String amount_text;

    CountDownTimer countdown;

    IntentFilter filter;
    BroadcastReceiver receiver;
    boolean receiverReg = false;

    MediaPlayer mp;
    Vibrator v;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.create_transaction_page);
        activeContext = this;
        theActiveContext = this;
        activeClass = this.getClass();
        setReceiver();
        target = (EditText) findViewById(R.id.target_user_text);
        amount = (EditText) findViewById(R.id.amount_text);
        spinner = (Spinner) findViewById(R.id.spinner);
        spinnerMessage = spinner.getItemAtPosition(0).toString();
        spinner.setOnItemSelectedListener(new Spinner.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                spinnerMessage = spinner.getItemAtPosition(position).toString();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
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
        Intent intent = new Intent(activeContext, ProfilePage.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        startActivity(intent);
        finish();
    }

    public void setReceiver() {
        filter = new IntentFilter();
        filter.addAction("serverResponseCurrency");
        filter.addAction("messageBroadcast");
        filter.addAction("serverResponseTransaction");
        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context activeContext, Intent intent) {
                if (intent.getAction().equals("serverResponseCurrency")) {
                    response = intent.getStringExtra("result");
                }
                else if (intent.getAction().equals("serverResponseTransaction")) {
                    response = intent.getStringExtra("result");
                }
                else if (intent.getAction().equals("messageBroadcast")) {
                    Log.e("TAG", "RECEIVED NOTIFICATION!");
                    Uri defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
                    if (mp != null) {
                        if (mp.isPlaying()) {
                            mp.stop();
                        }
                    }
                    mp = MediaPlayer.create(getApplicationContext(), defaultSoundUri);
                    mp.start();
                    //Vibrate
                    v = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
                    // Vibrate for 500 milliseconds
                    v.vibrate(1000);
                    if (activeDialog != null) {
                        activeDialog.dismissWithAnimation();
                    }
                    activeDialog = new SweetAlertDialog(theActiveContext)
                            .setTitleText("Transaction processed!")
                            .setContentText(intent.getStringExtra("message"))
                            .setConfirmText("Ok")
                            .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                                @Override
                                public void onClick(SweetAlertDialog sweetAlertDialog) {
                                    if (mp != null) {
                                        if (mp.isPlaying()) {
                                            mp.stop();
                                        }
                                    }
                                    if (v != null) {
                                        v.cancel();
                                    }
                                    activeDialog.dismissWithAnimation();
                                }
                            });
                    activeDialog.show();
                }
            }
        };
        if (!receiverReg) {
            LocalBroadcastManager.getInstance(this).registerReceiver(receiver, filter);
            receiverReg = true;
        }
    }

    public void onSendClick(View v) {
        target_text = target.getText().toString();
        amount_text = amount.getText().toString();
        if (target_text.equals("")) {
            //If email is empty
            pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                    .setTitleText("Send to who?")
                    .setContentText("You have to designate who you want to send the money to")
                    .setConfirmText("Got it")
                    .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                        @Override
                        public void onClick(SweetAlertDialog sDialog) {
                            pDialog.dismissWithAnimation();
                            target.setHintTextColor(Color.RED);
                            target.setText("");
                        }
                    });
            pDialog.show();
        }
        if (amount_text.equals("")) {
            //If email is empty
            pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                    .setTitleText("How much?")
                    .setContentText("You have to define an amount to send")
                    .setConfirmText("Got it")
                    .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                        @Override
                        public void onClick(SweetAlertDialog sDialog) {
                            pDialog.dismissWithAnimation();
                            amount.setHintTextColor(Color.RED);
                            amount.setText("");
                        }
                    });
            pDialog.show();
        }

        if (!amount_text.equals("") && !target_text.equals("")) {
            String data = prepareData();
            String url = server+"createTransaction.php?pass=masterpass";

            Intent msgIntent = new Intent( activeContext, TalkToServer.class);
            //Here we define the parameters (url, data)
            //basically the target php script and the data that's going to be send to it
            msgIntent.putExtra("url", url);
            msgIntent.putExtra("data",data);
            startService(msgIntent);

            pDialog = new SweetAlertDialog(this, SweetAlertDialog.PROGRESS_TYPE);
            pDialog.getProgressHelper().setBarColor(Color.parseColor("#A5DC86"));
            pDialog.setTitleText("Creating transaction");
            pDialog.setCancelable(false);
            pDialog.show();
            activeDialog = pDialog;
            countdown = new CountDownTimer(3000, 1000) {
                @Override
                public void onTick(long l) {

                }

                @Override
                public void onFinish() {
                    pDialog.dismissWithAnimation();
                    if (response!=null) {
                        if (!response.contains("Transaction submitted")) {
                            pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                                    .setTitleText("Error")
                                    .setContentText("Something went wrong!")
                                    .setConfirmText("Got it")
                                    .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                                        @Override
                                        public void onClick(SweetAlertDialog sDialog) {
                                            pDialog.dismissWithAnimation();
                                        }
                                    });
                            pDialog.show();
                        }
                        else if (response.contains("Transaction submitted")) {
                            pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.SUCCESS_TYPE);
                            pDialog.setTitleText("Success");
                            pDialog.setContentText("The transaction has been created, it will be processed as soon as possible");
                            if (activeDialog!=null) {
                                if (activeDialog.isShowing()) {
                                    activeDialog.dismissWithAnimation();
                                }
                            }
                            pDialog.show();
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
            if (spinnerMessage.equals("BTC")) {
                obj.put("type","send_btc");
            }
            else if (spinnerMessage.equals("ETH")) {
                obj.put("type","send_eth");
            }
            obj.put("source_user",devEmail);
            obj.put("target_user",target_text); //Here it should be ideally generating it in a proper way not hardcoded of course
            obj.put("amount", Float.valueOf(amount_text));
            ar.put(obj);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return ar.toString();
    }

}