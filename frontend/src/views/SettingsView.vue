<template>
  <div class="settings-container">
    <div class="settings-header">
      <h1>⚙️ 系统设置</h1>
      <p class="subtitle">配置应用的各项参数</p>
    </div>

    <div class="settings-content">
      <!-- 加星星上限设置 -->
      <div class="setting-card">
        <div class="setting-info">
          <h3>⭐ 每次加星星上限</h3>
          <p class="description">设置一次性可以添加的最大星星数量</p>
        </div>
        <div class="setting-control">
          <input
            type="number"
            v-model.number="maxStarsPerAdd"
            class="setting-input"
            min="1"
            max="999"
          />
          <span class="unit">颗</span>
        </div>
      </div>

      <!-- 加星星理由设置 -->
      <div class="setting-card">
        <div class="setting-info">
          <h3>➕ 加星星理由标签</h3>
          <p class="description">自定义加星星时的快捷理由选项</p>
        </div>
        <div class="reasons-list">
          <div
            v-for="(reason, index) in addStarReasons"
            :key="index"
            class="reason-item"
          >
            <input
              type="text"
              v-model="reason.emoji"
              class="emoji-input"
              placeholder="😊"
              maxlength="2"
            />
            <input
              type="text"
              v-model="reason.text"
              class="text-input"
              placeholder="理由文字"
              maxlength="10"
            />
            <div class="amount-input-wrapper">
              <input
                type="number"
                v-model.number="reason.amount"
                class="amount-input"
                min="1"
                max="999"
                placeholder="1"
              />
              <span class="amount-unit">⭐</span>
            </div>
            <button class="btn-remove" @click="removeAddReason(index)">
              ✕
            </button>
          </div>
          <button class="btn-add-reason" @click="addAddReason">
            ➕ 添加理由
          </button>
        </div>
      </div>

      <!-- 减星星理由设置 -->
      <div class="setting-card">
        <div class="setting-info">
          <h3>➖ 减星星理由标签</h3>
          <p class="description">自定义减星星时的快捷理由选项</p>
        </div>
        <div class="reasons-list">
          <div
            v-for="(reason, index) in subtractStarReasons"
            :key="index"
            class="reason-item"
          >
            <input
              type="text"
              v-model="reason.emoji"
              class="emoji-input"
              placeholder="😢"
              maxlength="2"
            />
            <input
              type="text"
              v-model="reason.text"
              class="text-input"
              placeholder="理由文字"
              maxlength="10"
            />
            <div class="amount-input-wrapper">
              <input
                type="number"
                v-model.number="reason.amount"
                class="amount-input"
                min="1"
                max="999"
                placeholder="1"
              />
              <span class="amount-unit">⭐</span>
            </div>
            <button class="btn-remove" @click="removeSubtractReason(index)">
              ✕
            </button>
          </div>
          <button class="btn-add-reason" @click="addSubtractReason">
            ➕ 添加理由
          </button>
        </div>
      </div>

      <div v-if="saveMessage" class="save-message" :class="saveMessageType">
        {{ saveMessage }}
      </div>

      <div class="setting-actions">
        <button class="btn-save" @click="handleSaveAll" :disabled="saving">
          {{ saving ? '保存中...' : '💾 保存所有设置' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { settingsApi } from '@/api/settings';
import type { ReasonTag } from '@/types';

const maxStarsPerAdd = ref(100);
const addStarReasons = ref<ReasonTag[]>([]);
const subtractStarReasons = ref<ReasonTag[]>([]);
const saving = ref(false);
const saveMessage = ref('');
const saveMessageType = ref<'success' | 'error'>('success');

const loadSettings = async () => {
  try {
    // Load max stars
    const maxStars = await settingsApi.get('max_stars_per_add');
    maxStarsPerAdd.value = maxStars;

    // Load add reasons
    const addReasons = await settingsApi.get('add_star_reasons');
    addStarReasons.value = addReasons || [];

    // Load subtract reasons
    const subtractReasons = await settingsApi.get('subtract_star_reasons');
    subtractStarReasons.value = subtractReasons || [];
  } catch (error) {
    console.error('Failed to load settings:', error);
    saveMessage.value = '加载设置失败';
    saveMessageType.value = 'error';
  }
};

const addAddReason = () => {
  addStarReasons.value.push({ emoji: '', text: '', amount: 1 });
};

const removeAddReason = (index: number) => {
  addStarReasons.value.splice(index, 1);
};

const addSubtractReason = () => {
  subtractStarReasons.value.push({ emoji: '', text: '', amount: 1 });
};

const removeSubtractReason = (index: number) => {
  subtractStarReasons.value.splice(index, 1);
};

const handleSaveAll = async () => {
  if (saving.value) return;

  // Validate
  if (maxStarsPerAdd.value < 1) {
    saveMessage.value = '星星上限必须大于 0';
    saveMessageType.value = 'error';
    return;
  }

  // Filter out empty reasons
  const validAddReasons = addStarReasons.value.filter(
    (r) => r.emoji.trim() && r.text.trim()
  );
  const validSubtractReasons = subtractStarReasons.value.filter(
    (r) => r.emoji.trim() && r.text.trim()
  );

  try {
    saving.value = true;
    saveMessage.value = '';

    // Save all settings
    await Promise.all([
      settingsApi.update('max_stars_per_add', maxStarsPerAdd.value),
      settingsApi.update('add_star_reasons', validAddReasons),
      settingsApi.update('subtract_star_reasons', validSubtractReasons),
    ]);

    // Update local state with valid reasons
    addStarReasons.value = validAddReasons;
    subtractStarReasons.value = validSubtractReasons;

    saveMessage.value = '✓ 所有设置已保存';
    saveMessageType.value = 'success';

    // Clear message after 3 seconds
    setTimeout(() => {
      saveMessage.value = '';
    }, 3000);
  } catch (error: any) {
    saveMessage.value = error.response?.data?.message || '保存失败';
    saveMessageType.value = 'error';
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>

<style scoped>
.settings-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 20px;
}

.settings-header {
  text-align: center;
  margin-bottom: 40px;
}

.settings-header h1 {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: #666;
  margin: 0;
}

.settings-content {
  background: white;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.setting-card {
  padding: 24px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 16px;
  margin-bottom: 24px;
}

.setting-card:last-of-type {
  margin-bottom: 24px;
}

.setting-info h3 {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin: 0 0 8px 0;
}

.description {
  font-size: 14px;
  color: #666;
  margin: 0 0 16px 0;
}

.setting-control {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.setting-input {
  width: 120px;
  height: 56px;
  font-size: 32px;
  font-weight: bold;
  text-align: center;
  border: 3px solid #84fab0;
  border-radius: 12px;
  color: #4caf50;
  transition: all 0.2s;
}

.setting-input:focus {
  outline: none;
  box-shadow: 0 0 0 4px rgba(132, 250, 176, 0.2);
  transform: scale(1.05);
}

.unit {
  font-size: 18px;
  font-weight: 600;
  color: #666;
}

.reasons-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.reason-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.emoji-input {
  width: 60px;
  height: 44px;
  font-size: 24px;
  text-align: center;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  transition: border-color 0.2s;
}

.emoji-input:focus {
  outline: none;
  border-color: #84fab0;
}

.text-input {
  flex: 1;
  height: 44px;
  padding: 0 16px;
  font-size: 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  transition: border-color 0.2s;
}

.text-input:focus {
  outline: none;
  border-color: #84fab0;
}

.amount-input-wrapper {
  display: flex;
  align-items: center;
  gap: 4px;
  background: white;
  padding: 4px 12px;
  border-radius: 8px;
  border: 2px solid #e0e0e0;
  transition: border-color 0.2s;
}

.amount-input-wrapper:focus-within {
  border-color: #84fab0;
}

.amount-input {
  width: 50px;
  height: 36px;
  font-size: 18px;
  font-weight: 600;
  text-align: center;
  border: none;
  background: transparent;
  color: #4caf50;
}

.amount-input:focus {
  outline: none;
}

.amount-unit {
  font-size: 16px;
}

.btn-remove {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: #ffebee;
  color: #c62828;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-remove:hover {
  background: #ef5350;
  color: white;
  transform: scale(1.1);
}

.btn-add-reason {
  padding: 12px 24px;
  border: 2px dashed #84fab0;
  border-radius: 12px;
  background: white;
  color: #4caf50;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-add-reason:hover {
  background: #e8f5e9;
  border-color: #4caf50;
}

.save-message {
  padding: 16px;
  border-radius: 12px;
  text-align: center;
  font-weight: 600;
  margin-bottom: 24px;
  animation: slideIn 0.3s ease;
}

.save-message.success {
  background: #e8f5e9;
  color: #2e7d32;
}

.save-message.error {
  background: #ffebee;
  color: #c62828;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.setting-actions {
  display: flex;
  justify-content: center;
}

.btn-save {
  padding: 16px 48px;
  font-size: 18px;
  font-weight: 600;
  border: none;
  border-radius: 16px;
  background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
  color: white;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(132, 250, 176, 0.4);
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(132, 250, 176, 0.5);
}

.btn-save:active:not(:disabled) {
  transform: translateY(0);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .settings-container {
    padding: 20px 16px;
  }

  .settings-header h1 {
    font-size: 24px;
  }

  .settings-content {
    padding: 20px;
  }

  .setting-card {
    padding: 16px;
  }

  .reason-item {
    flex-wrap: wrap;
  }

  .emoji-input {
    width: 50px;
    height: 40px;
    font-size: 20px;
  }

  .text-input {
    flex: 1;
    min-width: 150px;
  }

  .btn-remove {
    width: 32px;
    height: 32px;
    font-size: 16px;
  }
}
</style>
