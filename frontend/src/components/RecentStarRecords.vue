<template>
  <div class="recent-records">
    <div class="records-header">
      <h2 class="records-title">📋 最近记录</h2>
    </div>

    <div v-if="loading" class="loading">加载中...</div>

    <div v-else-if="records.length === 0" class="empty-state">
      <p class="empty-text">还没有记录哦～</p>
    </div>

    <div v-else class="records-list">
      <div
        v-for="record in records"
        :key="record.id"
        class="record-item"
        :class="recordClass(record.type)"
      >
        <div class="record-avatar">
          <img
            v-if="record.child?.avatar"
            :src="record.child.avatar"
            :alt="record.child.name"
          />
          <span v-else class="avatar-placeholder">
            {{ record.child?.gender === 'male' ? '👦' : '👧' }}
          </span>
        </div>

        <div class="record-content">
          <div class="record-main">
            <span class="child-name">{{ record.child?.name }}</span>
            <span class="record-type">{{ typeText(record.type) }}</span>
            <span class="record-amount" :class="amountClass(record.type)">
              {{ formatAmount(record.amount) }}⭐
            </span>
          </div>
          <div class="record-details">
            <span v-if="record.reason" class="record-reason">
              {{ record.reason }}
            </span>
            <span v-if="record.reward" class="record-reward">
              兑换：{{ record.reward.name }}
            </span>
            <span class="record-time">{{ record.created_at }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { starsApi } from '@/api/stars';
import type { StarRecord } from '@/types';

interface Props {
  limit?: number;
  autoRefresh?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  limit: 10,
  autoRefresh: false,
});

const records = ref<StarRecord[]>([]);
const loading = ref(true);

const loadRecords = async () => {
  try {
    loading.value = true;
    records.value = await starsApi.getRecent(props.limit);
  } catch (error) {
    console.error('Failed to load recent records:', error);
  } finally {
    loading.value = false;
  }
};

const recordClass = (type: string) => {
  return `record-${type}`;
};

const amountClass = (type: string) => {
  if (type === 'add') return 'amount-add';
  if (type === 'subtract') return 'amount-subtract';
  return 'amount-redeem';
};

const typeText = (type: string) => {
  if (type === 'add') return '获得';
  if (type === 'subtract') return '扣除';
  return '兑换';
};

const formatAmount = (amount: number) => {
  return Math.abs(amount);
};

onMounted(() => {
  loadRecords();
});

// Expose refresh method for parent component
defineExpose({
  refresh: loadRecords,
});
</script>

<style scoped>
.recent-records {
  background: white;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  margin: 0 auto 30px;
}

.records-header {
  margin-bottom: 20px;
}

.records-title {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  margin: 0;
  text-align: center;
}

.loading {
  text-align: center;
  padding: 40px 20px;
  font-size: 16px;
  color: #999;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
}

.empty-text {
  font-size: 16px;
  color: #999;
  margin: 0;
}

.records-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.record-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border-radius: 12px;
  transition: all 0.2s;
  border-left: 4px solid transparent;
}

.record-item:hover {
  transform: translateX(4px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.record-add {
  background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
  border-left-color: #4caf50;
}

.record-subtract {
  background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
  border-left-color: #ff9800;
}

.record-redeem {
  background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
  border-left-color: #9c27b0;
}

.record-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
  background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.record-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  font-size: 28px;
}

.record-content {
  flex: 1;
  min-width: 0;
}

.record-main {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
  flex-wrap: wrap;
}

.child-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.record-type {
  font-size: 14px;
  color: #666;
}

.record-amount {
  font-size: 18px;
  font-weight: bold;
  margin-left: auto;
}

.amount-add {
  color: #4caf50;
}

.amount-subtract {
  color: #ff9800;
}

.amount-redeem {
  color: #9c27b0;
}

.record-details {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  font-size: 13px;
  color: #666;
}

.record-reason {
  padding: 2px 8px;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 4px;
}

.record-reward {
  padding: 2px 8px;
  background: rgba(156, 39, 176, 0.1);
  border-radius: 4px;
  color: #9c27b0;
}

.record-time {
  margin-left: auto;
  opacity: 0.7;
}

@media (max-width: 768px) {
  .recent-records {
    padding: 16px;
    border-radius: 16px;
  }

  .records-title {
    font-size: 20px;
  }

  .record-item {
    padding: 12px;
    gap: 12px;
  }

  .record-avatar {
    width: 40px;
    height: 40px;
  }

  .avatar-placeholder {
    font-size: 24px;
  }

  .child-name {
    font-size: 15px;
  }

  .record-type {
    font-size: 13px;
  }

  .record-amount {
    font-size: 16px;
    width: 100%;
    margin-left: 0;
    margin-top: 4px;
  }

  .record-details {
    font-size: 12px;
  }

  .record-time {
    width: 100%;
    margin-left: 0;
    margin-top: 4px;
  }
}
</style>
