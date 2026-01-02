<template>
    <div class="voip-softphone-wrapper">
        <!-- Floating toggle button -->
        <div v-if="!isOpen" class="voip-toggle-btn" @click="toggle" title="Open VoIP Dialer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
            </svg>
        </div>

        <!-- Softphone Panel -->
        <div class="voip-softphone" :class="{ 'open': isOpen }">
            <!-- Header -->
            <div class="softphone-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <h4>VoIP</h4>
                <button @click="toggle" class="close-btn">×</button>
            </div>
        
            <!-- Tabs -->
            <div class="softphone-tabs">
                <div class="tab" :class="{ active: activeTab === 'contacts' }" @click="activeTab = 'contacts'">
                    Contacts
                </div>
                <div class="tab" :class="{ active: activeTab === 'recent' }" @click="activeTab = 'recent'">
                    Recent Calls
                </div>
                <div class="tab" :class="{ active: activeTab === 'keypad' }" @click="activeTab = 'keypad'">
                    Keypad
                </div>
            </div>

            <div class="softphone-body">
                <!-- Contacts Tab -->
                <div v-if="activeTab === 'contacts'" class="contacts-tab">
                    <div class="search-box">
                        <input 
                            type="text" 
                            v-model="contactSearch" 
                            placeholder="Search" 
                            class="search-input"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </div>
                    <div class="contacts-list">
                        <div 
                            v-for="contact in filteredContacts" 
                            :key="contact.id" 
                            class="contact-item"
                            @click="callContact(contact)"
                        >
                            <div class="contact-avatar" :style="{ backgroundColor: contact.color }">
                                {{ contact.initials }}
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">{{ contact.name }}</div>
                                <div class="contact-number">{{ contact.phone }}</div>
                            </div>
                            <button class="call-icon-btn" @click.stop="callContact(contact)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Calls Tab -->
                <div v-if="activeTab === 'recent'" class="recent-tab">
                    <div class="recent-list">
                        <div 
                            v-for="call in recentCalls" 
                            :key="call.id" 
                            class="recent-item"
                        >
                            <div class="call-avatar" :class="call.direction">
                                <svg v-if="call.direction === 'outbound'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 7 23 1 17 1"></polyline>
                                    <line x1="13" y1="11" x2="23" y2="1"></line>
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="1 7 1 1 7 1"></polyline>
                                    <line x1="11" y1="11" x2="1" y2="1"></line>
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <div class="call-info">
                                <div class="call-name">{{ call.contact_name || 'Unknown Contact' }}</div>
                                <div class="call-number">{{ call.direction === 'inbound' ? call.from_number : call.to_number }}</div>
                                <div class="call-meta">
                                    <span class="call-date">{{ formatCallDate(call.created_at) }}</span>
                                    <span v-if="call.duration" class="call-duration">{{ formatDuration(call.duration) }}</span>
                                </div>
                            </div>
                            <div class="call-status" :class="call.status">
                                {{ call.status }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keypad Tab -->
                <div v-if="activeTab === 'keypad'" class="keypad-tab">
                    <!-- Active Call Screen -->
                    <div v-if="activeCall || incomingCall" class="active-call-screen">
                        <div class="call-info-display">
                            <div class="caller-avatar">
                                {{ getCallerInitials() }}
                            </div>
                            <div class="caller-name">{{ getCallerName() }}</div>
                            <div class="caller-number">{{ getCallerNumber() }}</div>
                            <div class="call-status-text">{{ callStatus }}</div>
                            <div v-if="callDuration > 0" class="call-timer">{{ formatDuration(callDuration) }}</div>
                        </div>

                        <!-- Incoming Call Actions -->
                        <div v-if="incomingCall && !activeCall" class="incoming-actions">
                            <button @click="acceptCall" class="action-btn accept-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span>Accept</span>
                            </button>
                            <button @click="rejectCall" class="action-btn reject-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.42 19.42 0 0 1-3.33-2.67m-2.67-3.34a19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91"></path>
                                    <line x1="23" y1="1" x2="1" y2="23"></line>
                                </svg>
                                <span>Reject</span>
                            </button>
                        </div>

                        <!-- In-Call Actions -->
                        <div v-else-if="activeCall" class="in-call-controls">
                            <button @click="toggleMute" class="control-btn" :class="{ active: isMuted }">
                                <svg v-if="!isMuted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" y1="19" x2="12" y2="23"></line>
                                    <line x1="8" y1="23" x2="16" y2="23"></line>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                    <path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"></path>
                                    <path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"></path>
                                    <line x1="12" y1="19" x2="12" y2="23"></line>
                                    <line x1="8" y1="23" x2="16" y2="23"></line>
                                </svg>
                                <span>{{ isMuted ? 'Unmute' : 'Mute' }}</span>
                            </button>
                            <button @click="toggleKeypad" class="control-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                <span>Keypad</span>
                            </button>
                            <button @click="hangup" class="control-btn hangup-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.42 19.42 0 0 1-3.33-2.67m-2.67-3.34a19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91"></path>
                                    <line x1="23" y1="1" x2="1" y2="23"></line>
                                </svg>
                                <span>End Call</span>
                            </button>
                        </div>
                    </div>

                    <!-- Idle Keypad -->
                    <div v-else class="keypad-container">
                        <div class="number-display">
                            <input 
                                type="tel" 
                                v-model="phoneNumber" 
                                placeholder="" 
                                class="number-input"
                                readonly
                            />
                            <button v-if="phoneNumber" @click="phoneNumber = ''" class="clear-btn">×</button>
                        </div>

                        <div class="keypad-grid">
                            <button v-for="key in keypadKeys" :key="key.value" @click="pressKey(key.value)" class="keypad-btn">
                                <span class="key-number">{{ key.value }}</span>
                                <span v-if="key.letters" class="key-letters">{{ key.letters }}</span>
                            </button>
                        </div>

                        <div class="keypad-actions">
                            <button @click="makeCall" class="call-button" :disabled="!phoneNumber">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- DTMF Keypad Overlay (during call) -->
                    <div v-if="showDTMFKeypad && activeCall" class="dtmf-overlay">
                        <div class="dtmf-header">
                            <h5>Send DTMF</h5>
                            <button @click="toggleKeypad" class="close-dtmf">×</button>
                        </div>
                        <div class="keypad-grid small">
                            <button v-for="key in keypadKeys" :key="key.value" @click="sendDTMF(key.value)" class="keypad-btn">
                                <span class="key-number">{{ key.value }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Device } from '@twilio/voice-sdk';

export default {
    data() {
        return {
            isOpen: false,
            activeTab: 'keypad',
            phoneNumber: '',
            device: null,
            activeCall: null,
            incomingCall: null,
            callStatus: 'Initializing...',
            callDuration: 0,
            timerInterval: null,
            isMuted: false,
            showDTMFKeypad: false,
            contactSearch: '',
            contacts: [],
            recentCalls: [],
            keypadKeys: [
                { value: '1', letters: '' },
                { value: '2', letters: 'ABC' },
                { value: '3', letters: 'DEF' },
                { value: '4', letters: 'GHI' },
                { value: '5', letters: 'JKL' },
                { value: '6', letters: 'MNO' },
                { value: '7', letters: 'PQRS' },
                { value: '8', letters: 'TUV' },
                { value: '9', letters: 'WXYZ' },
                { value: '*', letters: '' },
                { value: '0', letters: '+' },
                { value: '#', letters: '' },
            ],
            avatarColors: ['#4A90E2', '#50C878', '#FF6B6B', '#9B59B6', '#F39C12', '#1ABC9C']
        };
    },
    computed: {
        filteredContacts() {
            if (!this.contactSearch) return this.contacts;
            const search = this.contactSearch.toLowerCase();
            return this.contacts.filter(c => 
                c.name.toLowerCase().includes(search) || 
                c.phone.includes(search)
            );
        }
    },
    mounted() {
        this.setupTwilio();
        this.loadContacts();
        this.loadRecentCalls();
        
        // Listen for global call events
        window.addEventListener('voip:call', this.handleExternalCall);
    },
    beforeUnmount() {
        if (this.device) {
            this.device.destroy();
        }
        window.removeEventListener('voip:call', this.handleExternalCall);
    },
    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
        },
        pressKey(key) {
            this.phoneNumber += key;
        },
        sendDTMF(key) {
            if (this.activeCall) {
                this.activeCall.sendDigits(key);
            }
        },
        toggleKeypad() {
            this.showDTMFKeypad = !this.showDTMFKeypad;
        },
        async setupTwilio() {
            try {
                const response = await axios.post('/api/voip/token');
                const token = response.data.token;

                this.device = new Device(token, {
                    codecPreferences: ['opus', 'pcmu'],
                    fakeLocalDTMF: true,
                    enableRingingState: true,
                });

                this.device.on('registered', () => {
                    console.log('Twilio Device Registered');
                    this.callStatus = 'Ready';
                });

                this.device.on('error', (error) => {
                    console.error('Twilio Error', error);
                    this.callStatus = 'Error: ' + error.message;
                });

                this.device.on('incoming', (call) => {
                    console.log('Incoming call from:', call.parameters.From);
                    this.incomingCall = call;
                    this.activeTab = 'keypad';
                    this.isOpen = true;
                    this.callStatus = 'Incoming call...';
                    
                    // Play ringtone
                    this.playRingtone();
                    
                    call.on('cancel', () => {
                        this.incomingCall = null;
                        this.callStatus = 'Missed call';
                        this.stopRingtone();
                    });
                });
                
                this.device.on('connect', (call) => {
                    console.log('Call connected');
                    this.activeCall = call;
                    this.incomingCall = null;
                    this.callStatus = 'Connected';
                    this.startTimer();
                    this.stopRingtone();
                });

                this.device.on('disconnect', (call) => {
                    console.log('Call ended');
                    this.activeCall = null;
                    this.incomingCall = null;
                    this.callStatus = 'Call ended';
                    this.stopTimer();
                    this.phoneNumber = '';
                    this.isMuted = false;
                    this.showDTMFKeypad = false;
                    this.loadRecentCalls();
                });

                await this.device.register();

            } catch (e) {
                console.error('Error setting up Twilio', e);
                this.callStatus = 'Setup failed';
            }
        },
        async makeCall() {
            if (!this.device || !this.phoneNumber) return;

            try {
                const params = {
                    To: this.phoneNumber
                };

                const call = await this.device.connect({ params });
                this.callStatus = 'Calling...';
                this.activeCall = call;
            } catch (error) {
                console.error('Error making call:', error);
                this.callStatus = 'Call failed';
            }
        },
        hangup() {
            if (this.activeCall) {
                this.activeCall.disconnect();
            }
            this.activeCall = null;
            this.stopRingtone();
        },
        acceptCall() {
            if (this.incomingCall) {
                this.incomingCall.accept();
                this.activeCall = this.incomingCall;
                this.incomingCall = null;
            }
        },
        rejectCall() {
            if (this.incomingCall) {
                this.incomingCall.reject();
                this.incomingCall = null;
            }
            this.stopRingtone();
        },
        toggleMute() {
            if (this.activeCall) {
                this.isMuted = !this.isMuted;
                this.activeCall.mute(this.isMuted);
            }
        },
        startTimer() {
            this.callDuration = 0;
            this.timerInterval = setInterval(() => {
                this.callDuration++;
            }, 1000);
        },
        stopTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
            this.callDuration = 0;
        },
        formatDuration(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) {
                return `${h}:${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
            }
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        },
        formatCallDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} min ago`;
            if (diffHours < 24) return `${diffHours} hours ago`;
            
            return date.toLocaleDateString();
        },
        async loadContacts() {
            try {
                const response = await axios.get('/api/voip/contacts');
                this.contacts = response.data.data.map((contact, index) => ({
                    ...contact,
                    initials: this.getInitials(contact.name),
                    color: this.avatarColors[index % this.avatarColors.length]
                }));
            } catch (e) {
                console.error('Error loading contacts:', e);
            }
        },
        async loadRecentCalls() {
            try {
                const response = await axios.get('/api/voip/calls/history');
                this.recentCalls = response.data.data || [];
            } catch (e) {
                console.error('Error loading recent calls:', e);
            }
        },
        callContact(contact) {
            this.phoneNumber = contact.phone;
            this.activeTab = 'keypad';
            this.makeCall();
        },
        handleExternalCall(event) {
            // Handle click-to-call from other parts of the CRM
            if (event.detail && event.detail.number) {
                this.phoneNumber = event.detail.number;
                this.isOpen = true;
                this.activeTab = 'keypad';
                if (event.detail.autoCall) {
                    this.makeCall();
                }
            }
        },
        getInitials(name) {
            if (!name) return 'UC';
            const parts = name.split(' ');
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        },
        getCallerName() {
            if (this.incomingCall) {
                return this.incomingCall.customParameters.get('name') || 'Unknown Contact';
            }
            if (this.activeCall) {
                const params = this.activeCall.customParameters;
                return params.get('name') || this.phoneNumber || 'Unknown';
            }
            return '';
        },
        getCallerNumber() {
            if (this.incomingCall) {
                return this.incomingCall.parameters.From || '';
            }
            if (this.activeCall) {
                return this.phoneNumber || this.activeCall.parameters.To || '';
            }
            return this.phoneNumber || '';
        },
        getCallerInitials() {
            const name = this.getCallerName();
            return this.getInitials(name);
        },
        playRingtone() {
            // Simple browser notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Incoming Call', {
                    body: `Call from ${this.getCallerNumber()}`,
                    icon: '/path/to/phone-icon.png'
                });
            }
        },
        stopRingtone() {
            // Stop ringtone if implemented
        }
    }
}
</script>

<style scoped>
/* Container */
.voip-softphone-wrapper {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 10000;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
}

/* Toggle Button */
.voip-toggle-btn {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(14, 165, 233, 0.4);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.voip-toggle-btn:hover {
    box-shadow: 0 6px 25px rgba(14, 165, 233, 0.6);
    transform: scale(1.05);
}

/* Main Panel */
.voip-softphone {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 380px;
    max-height: 600px;
    background: #ffffff;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border-radius: 16px;
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.voip-softphone.open {
    display: flex;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Header */
.softphone-header {
    background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
    color: #ffffff;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.softphone-header h4 {
    flex: 1;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.close-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: background 0.2s;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Tabs */
.softphone-tabs {
    display: flex;
    background: #F8FAFC;
    border-bottom: 1px solid #E2E8F0;
}

.tab {
    flex: 1;
    padding: 12px 8px;
    text-align: center;
    font-size: 13px;
    font-weight: 500;
    color: #64748B;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.tab.active {
    color: #0EA5E9;
    border-bottom-color: #0EA5E9;
    background: #ffffff;
}

.tab:hover:not(.active) {
    background: #F1F5F9;
}

/* Body */
.softphone-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

/* Contacts Tab */
.contacts-tab {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.search-box {
    position: relative;
}

.search-input {
    width: 100%;
    padding: 10px 12px 10px 38px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.search-input:focus {
    border-color: #0EA5E9;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
}

.contacts-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-height: 450px;
    overflow-y: auto;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.contact-item:hover {
    background: #F1F5F9;
}

.contact-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 15px;
    flex-shrink: 0;
}

.contact-info {
    flex: 1;
    min-width: 0;
}

.contact-name {
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-number {
    font-size: 13px;
    color: #64748B;
}

.call-icon-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #0EA5E9;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}

.call-icon-btn:hover {
    background: #0284C7;
    transform: scale(1.05);
}

/* Recent Calls Tab */
.recent-tab {
    display: flex;
    flex-direction: column;
}

.recent-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-height: 500px;
    overflow-y: auto;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    transition: background 0.2s;
}

.recent-item:hover {
    background: #F1F5F9;
}

.call-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.call-avatar.outbound {
    background: #DBEAFE;
    color: #0284C7;
}

.call-avatar.inbound {
    background: #D1FAE5;
    color: #059669;
}

.call-info {
    flex: 1;
    min-width: 0;
}

.call-name {
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.call-number {
    font-size: 12px;
    color: #64748B;
}

.call-meta {
    display: flex;
    gap: 8px;
    margin-top: 2px;
}

.call-date {
    font-size: 11px;
    color: #94A3B8;
}

.call-duration {
    font-size: 11px;
    color: #94A3B8;
}

.call-status {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
    flex-shrink: 0;
}

.call-status.completed {
    background: #D1FAE5;
    color: #059669;
}

.call-status.missed {
    background: #FEE2E2;
    color: #DC2626;
}

.call-status.failed {
    background: #FEE2E2;
    color: #DC2626;
}

/* Keypad Tab */
.keypad-tab {
    position: relative;
}

/* Active Call Screen */
.active-call-screen {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 0;
}

.call-info-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 30px;
}

.caller-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 16px;
}

.caller-name {
    font-size: 18px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 4px;
}

.caller-number {
    font-size: 14px;
    color: #64748B;
    margin-bottom: 8px;
}

.call-status-text {
    font-size: 13px;
    color: #0EA5E9;
    font-weight: 500;
}

.call-timer {
    font-size: 20px;
    font-weight: 600;
    color: #1E293B;
    margin-top: 12px;
}

/* Incoming Actions */
.incoming-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 12px;
    border-radius: 12px;
    transition: background 0.2s;
}

.action-btn span {
    font-size: 13px;
    font-weight: 500;
}

.accept-btn {
    color: #10B981;
}

.accept-btn svg {
    width: 48px;
    height: 48px;
    padding: 12px;
    background: #10B981;
    color: white;
    border-radius: 50%;
}

.accept-btn:hover {
    background: #D1FAE5;
}

.reject-btn {
    color: #EF4444;
}

.reject-btn svg {
    width: 48px;
    height: 48px;
    padding: 12px;
    background: #EF4444;
    color: white;
    border-radius: 50%;
}

.reject-btn:hover {
    background: #FEE2E2;
}

/* In-Call Controls */
.in-call-controls {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.control-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    background: #F1F5F9;
    border: none;
    padding: 14px 18px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    color: #475569;
    font-size: 12px;
    font-weight: 500;
}

.control-btn:hover {
    background: #E2E8F0;
}

.control-btn.active {
    background: #0EA5E9;
    color: white;
}

.control-btn.hangup-btn {
    background: #EF4444;
    color: white;
}

.control-btn.hangup-btn:hover {
    background: #DC2626;
}

/* Idle Keypad */
.keypad-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.number-display {
    position: relative;
    display: flex;
    align-items: center;
}

.number-input {
    width: 100%;
    padding: 16px 40px 16px 16px;
    font-size: 22px;
    text-align: center;
    border: none;
    background: #F8FAFC;
    border-radius: 12px;
    outline: none;
    letter-spacing: 1px;
}

.clear-btn {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    font-size: 24px;
    color: #94A3B8;
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

.clear-btn:hover {
    background: #E2E8F0;
}

.keypad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.keypad-grid.small {
    gap: 8px;
}

.keypad-btn {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
    border: 1px solid #E2E8F0;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
}

.keypad-btn:hover {
    background: linear-gradient(135deg, #E2E8F0 0%, #CBD5E1 100%);
    transform: scale(1.05);
}

.keypad-btn:active {
    transform: scale(0.95);
}

.key-number {
    font-size: 24px;
    font-weight: 600;
    color: #1E293B;
}

.key-letters {
    font-size: 10px;
    color: #64748B;
    margin-top: -2px;
}

.keypad-actions {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.call-button {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    transition: all 0.2s;
}

.call-button:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.call-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* DTMF Overlay */
.dtmf-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    padding: 16px;
    border-radius: 12px;
    z-index: 10;
}

.dtmf-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.dtmf-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1E293B;
}

.close-dtmf {
    background: none;
    border: none;
    font-size: 28px;
    color: #64748B;
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    line-height: 1;
}

/* Scrollbar */
.contacts-list::-webkit-scrollbar,
.recent-list::-webkit-scrollbar {
    width: 6px;
}

.contacts-list::-webkit-scrollbar-thumb,
.recent-list::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 3px;
}

.contacts-list::-webkit-scrollbar-thumb:hover,
.recent-list::-webkit-scrollbar-thumb:hover {
    background: #94A3B8;
}
</style>
