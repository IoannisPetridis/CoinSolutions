package salamantex.coinsolutions.notifications;

/**
 * Created by EEUser on 05/12/2017.
 */

//callbacks interface for communication with service clients!
public interface Callbacks {

    public void messageReceived(String sender, String message);
    public void meetingNotification(String message);

}
